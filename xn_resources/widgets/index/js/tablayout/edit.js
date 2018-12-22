dojo.provide('xg.index.tablayout.edit');
dojo.require('xg.shared.util');
dojo.require('xg.shared.ContextHelpToggler'); // !used

(function($) {
	var qh = function(s) { return dojo.string.escape('html',s) };
	var stop = function(e) { e = e || window.event;
		if (e.stopPropagation) e.stopPropagation();
		if (e.preventDefault) e.preventDefault();
		e.cancelBubble = true;
		e.returnValue = false;
	}
	var trim = function(s) { return s.replace(/^\s+|\s+$/, '') };
	var setDisabled = function(value, nodes) {
		nodes = $(nodes);
		if (value) {
			nodes.attr('disabled','disabled');
			nodes.parents('dl').eq(0).addClass('disabled');
		} else {
			nodes.removeAttr('disabled');
			nodes.parents('dl').eq(0).removeClass('disabled');
		}
	}
	var tabList, tabEditor;

	//
	//** Tab List Manager
	//
	tabList = {
		tabContainer: undefined,		// UL with tabs
		activeTab: undefined,			// selected <LI>
		lastFixedTab: undefined,		// last "fixed" <LI>
		helpSign: undefined,			// Node with help sign
		helpBox: undefined,				// Node with help tip content
		helpMsg: undefined,				// Node with help message
		tabCnt: 0,						// Tab index seed
		maxTop: xgTabMgrMaxNonFixedTopTabs,	// Max number of top-level non-fixed tabs
		originalTabs: undefined,		// Original serialized tabs (data and order)
		suppressOnUnload: false,		// Suppresses the on-unload warning

		init: function() {
			this.tabContainer = dojo.byId('xj_tab_manager');
			this.helpSign = dojo.byId('xj_help');
			this.helpBox = dojo.byId('xj_help_box');
			this.helpMsg = dojo.byId('xj_help_msg');

			for(var i = 0;i<xgTabMgrTabs.length;i++) {
				this.appendTab(xgTabMgrTabs[i]);
			}
			this.appendTab({label:'', isFixed:true, isLastFakeTab:true}) // fake tab to be able to drag below the "manage" page.
			this.originalTabs = $.toJSON(this.serialize());
			this.enforceLimits();

			$('#xj_add_tab').bind('click',function(e) {
				stop(e);
				tabEditor.confirmUnsavedChanges(function(){
					tabList.appendTab({label:xg.index.nls.text('newTab')}, true);
				});
			});

			var draggingIdx = undefined;
			$(this.tabContainer).sortable({
				axis: 'y',
				cancel: '.fixed',
				start: function(e, ui) {
					var li = e.target.tagName == 'LI' ? e.target : $(e.target).parents('LI')[0], n;
					tabList.helpSign.style.display = 'none';
					tabList.helpBox.style.display = 'none';
					draggingIdx = li.getAttribute('idx');
				},
				update: function(e, ui) {
					var n = -1, item, cn = tabList.tabContainer.childNodes;
					if (!draggingIdx) return;
					for (var i = 0; i < cn.length; i++) {
						if (n < 0 && cn[i].getAttribute('idx') == draggingIdx) {n = 0;item = cn[i];}
						if (n >= 0 && $(cn[i]).hasClass('fixed')) n++;
					}
					if (n != 2) {
						// only items that are below the top fixed tabs and above the bottom fixed tab can have top-level.
						// 2 means: manage+fake tab
						$(item).addClass('sub');
					}
					if(item == tabList.activeTab) tabEditor.syncIsSubTabFlag();
					draggingIdx = undefined;
				},
				stop: function() { tabList.enforceLimits() },
				containment: 'parent'
			});
		},

		// Checks whether "isSubTab" must be disabled or enabled (used for fixed tabs)
		// Logic is: tab can be a subtab if there is 2 fixed tabs (manage + fake) below it.
		canChangeIsSubTabFlag: function(li) {
			var s = $(li);
			if (s.hasClass('fixed')) return false;
			if (!s.hasClass('sub')) return true;
			for(var cnt = 0, n = li;n;n = n.nextSibling) {
				if ($(n).hasClass('fixed')) cnt++;
			}
			return cnt == 2;
		},

		// Marks extra tabs as "not visible"
		enforceLimits: function() {
			var cn = this.tabContainer.childNodes, cls = 'notvisible xg_lightfont',
				subCnt = 0, topCnt = 0, hiding = 0,  	// top/sub counters and "hiding tabs state" flag
				firstHidden = undefined;				// first hidden tab/subtab (tab has a priority)
			this.helpSign.style.display = 'none';
			for (var i = 0, n; i < cn.length; i++) {
				n = $(cn[i]);
				if (n.hasClass('sub')) {
					if (++subCnt > xgTabMgrMaxSubTabs) { hiding = 1; if (!firstHidden) firstHidden = cn[i]; }
				} else {
					subCnt = 0;
					hiding = 0;
					if (n.hasClass('fixed')) { continue; }
					if (++topCnt > this.maxTop) {
						if (topCnt == this.maxTop+1) { firstHidden = cn[i] }
						hiding = 1;
					}
				}
				if (hiding) n.addClass(cls);
				else n.removeClass(cls);
			}
			if (firstHidden) {
				var o = xg.shared.util.getOffset(firstHidden, this.helpSign);
				this.helpSign.style.left = o.x + firstHidden.offsetWidth + 6 + 'px';
				this.helpSign.style.top = o.y + 6 + 'px';
				this.helpSign.style.display = '';
				this.helpMsg.innerHTML = $(firstHidden).hasClass('sub')
					? xg.index.nls.text('hiddenWarningSub', xgTabMgrMaxSubTabs)
					: xg.index.nls.text('hiddenWarningTop', xgTabMgrMaxTopTabs)
			};
		},

		removeTabProper: function(li, extraTabs) {
			if (li == this.activeTab) {
				tabEditor.onCancel();
			}
			var done = 0;
			extraTabs = extraTabs || [];
		    if (tabEditor.saveAllConfirm) tabEditor.saveAllConfirm.style.display = 'none';
			$(li).add(extraTabs).fadeOut(extraTabs ? 400 : 200, function() {
				if ( done++ ) return;
				for (var i = -1; i < extraTabs.length; i++) {
					var n = (i == -1 ? li : extraTabs[i]);
					tabList.tabContainer.removeChild(n);
				}
			    tabList.enforceLimits();
			});
		},

		// Removes tab from the list
		removeTab: function(li) {
			if (! $(li).hasClass('sub') && $(li.nextSibling).hasClass('sub')) {
				xg.shared.util.confirm({
					bodyText: xg.index.nls.text('removeConfirm'),
					onOk: function() {
						var extraTabs = [];
						for (var n = li.nextSibling; $(n).hasClass('sub'); n = n.nextSibling) {
							extraTabs.push(n);
						}
						tabList.removeTabProper(li, extraTabs);
					}
				});
			} else {
				return this.removeTabProper(li);
			}
		},

		// Sets tab as selected
		selectTabProper: function(li) {
			if (this.activeTab == li) return;
			if (li) $(li).addClass('selected').removeClass('xg_lightborder').removeClass('xg_lightfont');
			var hadActive = this.activeTab ? 1 : 0;
			if (hadActive) {
				$(this.activeTab).removeClass('selected').addClass('xg_lightborder');
				if ($(this.activeTab).hasClass('notvisible')) {
					$(this.activeTab).addClass('xg_lightfont');
				}
			}
			this.activeTab = li;
			if (li) tabEditor.onEdit(!hadActive);
		},

		selectTab: function(li, noConfirm) {
			this.helpBox.style.display = 'none';
			if (this.activeTab == li) return;
			if (this.activeTab && !noConfirm) {
				tabEditor.confirmUnsavedChanges(function() { tabList.selectTabProper(li) });
			} else {
				this.selectTabProper(li);
			}
		},

		// Appends tab to the list.
		// If UI is set to true, appens tab before the last "fixed" tab (manage)
		appendTab: function(tab, ui) {
			var li = document.createElement('li'), cls = 'xg_lightborder ';
			li.setAttribute('idx', (++this.tabCnt));
			li.setAttribute('tabKey', tab.tabKey||'');
			li.setAttribute('tabName', tab.label);
			li.setAttribute('tabUrl', tab.url||'');
			li.setAttribute('tabVisibility', tab.visibility || 'all');
			li.setAttribute('tabWindowTarget', tab.windowTarget||'');
			li.setAttribute('createPage', '');
			li.innerHTML = '<span>' + qh(tab.label) + '</span>' + (tab.isFixed ? '' : '<a href="#" class="delete">Delete</a>');

			if (tab.isSubTab) cls += 'sub ';
			if (tab.isFixed) {
				cls += 'fixed ';
			} else {
				$(li.lastChild).click(function(e) { stop(e); tabList.removeTab(li) })
			}
			if (tab.isLastFakeTab) {
				cls += 'last ';
			} else {
				if (tab.isFixed) this.lastFixedTab = li; // remember the last fixed tab
				li.onclick = function(e) { stop(e); tabList.selectTab(li) };
			}
			li.className = cls;

			if (ui) {
				li.style.display = 'none';
				this.tabContainer.insertBefore(li, this.lastFixedTab);
				$(li).fadeIn(300, function() { tabList.enforceLimits(); tabList.selectTab(li, true); });
			} else {
				this.tabContainer.appendChild(li);
			}
		},
		serialize: function() {
			var data = {}, cn = this.tabContainer.childNodes;
			for (var i = 0; i<cn.length-1; i++) { // we skip the last item: fake node to drag tabs below the last fixed tab
				var n = $(cn[i]), r = {
					url: n.attr('tabUrl'),
					label: n.attr('tabName'),
					isSubTab: n.hasClass('sub'),
					tabKey: n.attr('tabKey'),
					windowTarget: n.attr('tabWindowTarget'),
					visibility: n.attr('tabVisibility'),
					createPage: n.attr('createPage')
				};
				data[n.attr('idx')] = r;
			}
			return data;
		}
	}

	//
	//** Tab Editor (handles tabList.activeTab and handles global settings form)
	//
	tabEditor = {
		form: undefined,				// tab edit form
		globalForm: undefined,			// global settings form
		msgBox: undefined,				// message box
		saveAllConfirm: undefined,		// "changes have been saved" block
		tabNameNode: undefined,			// tabName node for event onblur
		isSubTabNode: undefined,		// isSubTab node for event onclick

		init: function() {
			this.form = dojo.byId('xj_tab_form');
			this.globalForm = dojo.byId('xj_tab_global_form');
			this.msgBox = dojo.byId('xj_msgbox');
			this.saveAllConfirm = dojo.byId('xj_save_all_confirm');
			this.tabNameNode = dojo.byId('tabName');
			this.isSubTabNode = dojo.byId('isSubTab');

			//this.form.save.onclick = function() { tabEditor.onSave() };
			//this.form.cancel.onclick = function() { tabEditor.onCancel() };

			this.globalForm.reset_all.onclick = function() {
				xg.shared.util.confirm({
					title: xg.index.nls.text('resetToDefaults'),
					bodyText: xg.index.nls.text('youNaviWillbeRestored'),
					onOk: function() {
						tabList.suppressOnUnload = true; // disable onunload warning, we're navigating away.
						window.location = tabEditor.globalForm.reset_all.getAttribute('_url');
					}
				});
			}
			this.globalForm.save.onclick = function() {
				if (tabList.activeTab) {
					// auto save the current tab
					if (!tabEditor.validateForm()) return;
					tabEditor.onSaveProper();
				}
				var data = tabList.serialize();
				if (tabList.activeTab && tabEditor.form.targetType[0].checked) {
					data[tabList.activeTab.getAttribute('idx')].createPage = true;
				}
				tabEditor.globalForm.layoutJson.value = $.toJSON(data);
				tabList.suppressOnUnload = true; // disable onunload warning, we're navigating away.
				tabEditor.globalForm.submit();
			}

			// register tabName event onblur and isSubTab event onclick
			this.tabNameNode.onblur = this.isSubTabNode.onclick = function() {
				tabEditor.onSave();
			}

			var a = $('#xj_reset_colors');
			a.click(function(e){
				stop(e);
				dojo.byId('xj_spinner2').style.display = '';
				xg.get(a.attr('_url'), {r:Math.random()}, function(http,d) {
					dojo.byId('xj_spinner2').style.display = 'none';
					var m = dojo.widget.manager;
					m.getWidgetById('xj_textColor')._pickColorQuick(d.textColor);
					m.getWidgetById('xj_textColorHover')._pickColorQuick(d.textColorHover);
					m.getWidgetById('xj_bgColor')._pickColorQuick(d.backgroundColor);
					m.getWidgetById('xj_bgColorHover')._pickColorQuick(d.backgroundColorHover);
				});
			});

			var x = (this.saveAllConfirm && this.saveAllConfirm.style.display != 'none');
			// show the first fixed tab
			tabList.selectTab($('li.fixed')[0]);
			if (x) this.saveAllConfirm.style.display = '';
		},

		// Syncronizes "isSubTab" flag from LI to tab form
		syncIsSubTabFlag: function() {
			setDisabled(!tabList.canChangeIsSubTabFlag(tabList.activeTab), this.form.isSubTab);
			this.form.isSubTab.checked = $(tabList.activeTab).hasClass('sub');
		},

		// Checks whether the current tab information has been changed
		hasChanges: function() {
			if (!tabList.activeTab) return false;
			var li = $(tabList.activeTab);
			var c = (this.form.tabName.value != li.attr('tabName'))
				|| (this.form.url.value != li.attr('tabUrl'))
				|| (this.form.visibility.value != li.attr('tabVisibility'))
				|| (this.form.isSubTab.checked != li.hasClass('sub'))
				|| (this.form.openInNewWindow.checked != (li.attr('tabWindowTarget')=='_blank'));
			return c;
		},

		/**
         *  Shows/hides message box above the tab form.
         *
		 *  @param	msg		string		Message text (undefined to hide)
		 *  @param	type	string		success|error|warning
		 *  @param	noEffects bool		Suppress effects
         */
		message: function(msg, type, noEffects) {
			if ("undefined" == typeof msg) return this.msgBox.style.display = 'none';
			this.msgBox.className = (type == 'success' ? 'success' : (type == 'error' ? 'errordesc' : 'notification xg_lightborder') );
            this.msgBox.innerHTML = msg;
			noEffects ? this.msgBox.style.display = '' : $(this.msgBox).fadeIn(300);
		},

		// Makes sure that there is no unsaved changes or they're confirmed. onConfirm:function specifies the "onOk" handler
		confirmUnsavedChanges: function(onConfirm) {
			if (!this.hasChanges()) {
				onConfirm();
			} else {
				tabEditor.onSave(onConfirm);
			}
		},

		clearValidationErrors: function() {
			$('#xj_dd_tabName, #xj_label_tabName').removeClass('error');
		},

		// Validates tab form. Returns TRUE if everything is ok and FALSE otherwise.
		validateForm: function() {
			// check that tab name is not empty
			this.form.tabName.value = trim(this.form.tabName.value);
			if (this.form.tabName.value.length < 1) {
				$('#xj_dd_tabName, #xj_label_tabName').addClass('error');
				this.message(xg.index.nls.text('youMustSpecifyTabName'), 'error');
				return false;
			}
			this.clearValidationErrors();
			this.message(undefined);
			return true;
		},

		/**
		 *  "Save tab" handler.
		 *
		 *  @param	createPage		bool		does this tab have an associated page that must be created?
		 */
		onSaveProper: function(createPage) {
			var li = $(tabList.activeTab);
			// save settings to li

			// a workaround to prevent blank names from squashing the li
			var tabName = trim(this.form.tabName.value).length > 0 ?
						this.form.tabName.value :
						li.attr('tabName');
			li.attr({
				tabName: tabName,
				tabUrl: this.form.url.value,
				tabVisibility: this.form.visibility.value,
				tabWindowTarget: this.form.openInNewWindow.checked ? '_blank':'',
				createPage: createPage || ''
			});
			tabList.activeTab.firstChild.innerHTML = qh(this.form.tabName.value);

			if (this.form.isSubTab.checked) li.addClass('sub');
			else li.removeClass('sub');
		},

		// tabEditor.form onSave
		//
		onSave: function(onConfirm) {
			if (!this.validateForm()) return;
			// use existing URL
			tabEditor.onSaveProper(this.form.targetType[0].checked ? '1' : '');
			tabList.enforceLimits();
			if (onConfirm) onConfirm();
			return;
		},

		// tabEditor.form onCancel
		onCancel: function() {
			$([this.form, this.msgBox]).fadeOut(300, function() {
				tabEditor.form.style.visibility = 'hidden';
				tabEditor.form.style.display = '';
			});
			tabList.selectTab(undefined, true);
		},

		// Called when tabs becomes selected. Handles the tab form behavior
		onEdit: function (effect) {
			if (this.saveAllConfirm) this.saveAllConfirm.style.display = 'none';

			this.message(undefined); // no effects
			this.clearValidationErrors();

			setDisabled(!tabList.canChangeIsSubTabFlag(tabList.activeTab), this.form.isSubTab);

			var li = $(tabList.activeTab);
			this.form.tabName.value = li.attr('tabName');

			setDisabled(li.hasClass('fixed'), [this.form.url, this.form.visibility, this.form.targetType[0],
				this.form.targetType[1], this.form.openInNewWindow]);

			this.form.isSubTab.checked = li.hasClass('sub');
			this.form.url.value = li.attr('tabUrl');
			this.form.visibility.value = li.attr('tabVisibility');
			this.form.openInNewWindow.checked = li.attr('tabWindowTarget') == '_blank';

			var createPage = li.attr('createPage');
			this.form.targetType[0].checked = createPage;
			this.form.targetType[1].checked = ! createPage;

			this.form.style.visibility = 'visible';
			if (effect) {
				this.form.style.display = 'none';
				$(this.form).fadeIn(300, function() { tabEditor.form.tabName.focus() });
			}
			if (li.hasClass('notvisible')) {
				this.message( li.hasClass('sub') ? xg.index.nls.text('hiddenWarningSub', xgTabMgrMaxSubTabs) : xg.index.nls.text('hiddenWarningTop', xgTabMgrMaxTopTabs), 'warning', !effect);
			}
		}
	}

	dojo.byId('xj_placeholder').style.display = 'none';
	tabList.init();
	tabEditor.init();

})(jQuery);
