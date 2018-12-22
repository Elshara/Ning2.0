dojo.provide('xg.events.EventEmbedModule');

dojo.require('xg.shared.util');
dojo.require('xg.shared.EditUtil');
dojo.require('xg.events.Scroller'); // A Scroller may be returned by the Ajax request [Jon Aquino 2008-04-04]

/**
 * Events embed block on main page.
 */
dojo.widget.defineWidget('xg.events.EventEmbedModule', dojo.widget.HtmlWidget, {
    isContainer: true,
    _url: '',            // URL for saving data
    _updateEmbedUrl: '', // URL for updating the embed
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
        dojo.html.hide(this.form);

        dojo.event.connect(this.form,'onsubmit',dojo.lang.hitch(this, this.save));
        dojo.event.connect(this.form.save,'onclick',dojo.lang.hitch(this, this.save));
        dojo.event.connect(this.form.cancel,'onclick',dojo.lang.hitch(this, this.cancel));
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

    /**
     * Call-back function to update the module body
     *
     * @param ui    jQuery.ui Object      The ui object which makes the callback
     */
    updateEmbed: function(ui) {
        var columnCount = this.module.parentNode.getAttribute('_columncount');
        var content = { columnCount: columnCount };
        dojo.io.bind({
            url: this._updateEmbedUrl,
            method: 'post',
            mimetype: 'text/json',
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
                if (moduleFooter) { this.module.appendChild(moduleFooter); }

                ui.item.css('visibility', '');

                // fix hover drag icon
                var handleDiv = this.module.getElementsByTagName('div')[0];
                if (dojo.html.hasClass(handleDiv, 'xg_handle')) dojo.style.hide(handleDiv);
            })
        });
    },

    save: function(event) {
        dojo.event.browser.stopEvent(event);
        var content = {}, el = this.form.elements;
        for (var i = 0; i<el.length;i++) {
            if (el[i].name) {
                content[el[i].name] = el[i].value;
            }
        }
        content.columnCount = this.module.parentNode.getAttribute('_columncount');
        
        dojo.io.bind({
            url: this._url,
            method: 'post',
            mimetype: 'text/json',
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
                    if (moduleFooter) { this.module.appendChild(moduleFooter); }
            })
        });
        this.hideForm();
    },
    cancel: function(event) {
        dojo.event.browser.stopEvent(event);
        this.hideForm();
    }
});
