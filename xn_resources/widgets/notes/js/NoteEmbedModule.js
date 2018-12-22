dojo.provide('xg.notes.NoteEmbedModule');

dojo.require('xg.shared.util');
dojo.require('xg.shared.EditUtil');

/**
 * Notes embed block on main page.
 */
dojo.widget.defineWidget('xg.notes.NoteEmbedModule', dojo.widget.HtmlWidget, {
    isContainer: true,
    _url: '',            // URL for saving data
	_isHomepage: 0,
	_visible: 0,
    fillInTemplate: function(args, frag) {
        this.module         = this.getFragNodeRef(frag);
        this.edit            = this.module.getElementsByTagName('p')[0];
        this.form            = this.module.getElementsByTagName('form')[0];

        dojo.html.show(this.edit);
        dojo.event.connect(this.edit.firstChild, 'onclick', dojo.lang.hitch(this, function(event) {
            dojo.event.browser.stopEvent(event);
			this._visible ? this.hideForm() : this.showForm();
        }));

        dojo.html.show(this.form);
        this.formHeight = this.form.offsetHeight;
        this.form.style.height = 0;
        this.changeDisplay();
        dojo.html.hide(this.form);

		dojo.event.connect(this.ctrl('display'),'onchange',dojo.lang.hitch(this, this.changeDisplay));
        dojo.event.connect(this.form,'onsubmit',dojo.lang.hitch(this, this.save));
        dojo.event.connect(this.form.save,'onclick',dojo.lang.hitch(this, this.save));
        dojo.event.connect(this.form.cancel,'onclick',dojo.lang.hitch(this, this.cancel));
    },
	changeDisplay: function() {
        var t = (this.ctrl('display').value == 'note');
		(t ? dojo.html.hide : dojo.html.show)(this.ctrl('from',1));
		(t ? dojo.html.hide : dojo.html.show)(this.ctrl('from',2));

		(t ? dojo.html.hide : dojo.html.show)(this.ctrl('count',1));
		(t ? dojo.html.hide : dojo.html.show)(this.ctrl('count',2));

		(t ? dojo.html.show : dojo.html.hide)(this.ctrl('title',1));
		(t ? dojo.html.show : dojo.html.hide)(this.ctrl('title',2));
	},
	// Mode: 0 - INPUT, 1 - parent DD, 2 - attached DT
	ctrl: function(mask,mode) {
		var els = this.form.elements, re = new RegExp(mask);
		for (var i = 0;i<els.length;i++) {
			if (!els[i].name || !els[i].name.match(re))
				continue;
			if (mode == 2) {
				var p = els[i].parentNode;
				while(p && p.tagName != 'DT') {
					p = p.previousSibling;
				}
				return p;
			} else if (mode == 1) {
				return els[i].parentNode;
			} else {
				return els[i];
			}
		}
		return;
	},
    showForm: function() {
        dojo.html.show(this.form);
        this._visible = 1;
        xg.shared.EditUtil.showModuleForm(this.form,this.formHeight,this.edit.firstChild);
    },
    hideForm: function() {
		this._visible = 0;
        xg.shared.EditUtil.hideModuleForm(this.form,this.formHeight,this.edit.firstChild);
    },
    save: function(event) {
        dojo.event.browser.stopEvent(event);
        var content = {}, el = this.form.elements;
        for (var i = 0; i<el.length;i++) {
            if (el[i].name) {
                content[el[i].name] = el[i].value;
            }
        }
        dojo.io.bind({
            url: this._url,
            method: 'post',
            mimetype: 'text/javascript',
            preventCache: true,
            encoding: 'utf-8',
            content: content,
            load: dojo.lang.hitch(this,  function(type, js, event){
                var ch = this.module.childNodes;
                for (var i = ch.length-1;i>=0;i--) {
                    if (ch[i].nodeType == 1 && ch[i].tagName == 'DIV' && ch[i].className.match(/xg_module_body|xg_module_foot/)) {
                        this.module.removeChild(ch[i]);
                    }
                }
                var div = document.createElement('DIV');
                div.innerHTML = js.moduleBodyAndFooter;
                var moduleBody = dojo.html.getElementsByClass('xg_module_body', div)[0];
                if (moduleBody) {
                    this.module.appendChild(moduleBody); 
                    xg.shared.util.parseWidgets(moduleBody);
                }
                var moduleFooter = dojo.html.getElementsByClass('xg_module_foot', div)[0];
                if (moduleFooter) { 
                    this.module.appendChild(moduleFooter); 
                    xg.shared.util.parseWidgets(moduleBody);
                }
            })
        });
        this.hideForm();
    },
    cancel: function(event) {
        dojo.event.browser.stopEvent(event);
        this.hideForm();
    }
});
