dojo.provide('xg.page.instance.edit');

dojo.require('xg.shared.util');
dojo.require('dojo.lfx.html');
dojo.require('xg.index.util.FormHelper');
dojo.require('xg.page.instance.PageEditor');

/**
 * Behavior for the page/instance/edit page.
 */
xg.page.instance.edit = {

    /**
     * Adds a PageEditor to the page container.
     *
     * @param title  tab text
     * @param directory  URL directory
     * @param displayTab  whether to display a tab for the page
     */
    addPage: function(title, directory, displayTab) {
        var editor = this.addPageProper(title, directory, displayTab, true);
        xg.shared.util.parseWidgets(editor);
        xg.index.util.FormHelper.scrollIntoView(editor);
        dojo.lfx.html.fadeIn(editor, 500, null, function() {
            editor.getElementsByTagName('input')[0].focus();
        }).play();
    },

    /**
     * Adds a PageEditor to the page container.
     *
     * @param title  tab text
     * @param directory  URL directory
     * @param displayTab  whether to display a tab for the page
     * @param transparent  whether to set the PageEditor's opacity to 0
     * @return  the new PageEditor
     */
    addPageProper: function(title, directory, displayTab, transparent) {
        var editor = this.createEditor(title, directory, displayTab);
        if (transparent) { dojo.style.setOpacity(editor, 0); }
        dojo.byId('xg_pages_container').appendChild(editor);
        xg.shared.util.fixImagesInIE(editor.getElementsByTagName('img'));
        return editor;
    },

    /**
     * Creates a PageEditor element
     *
     * @param title  tab text
     * @param directory  URL directory
     * @param displayTab  whether to display a tab for the page
     * @return  the new PageEditor
     */
    createEditor: function(title, directory, displayTab) {
        var container = document.createElement('div');
        container.innerHTML = ' \
                <div dojoType="PageEditor" class="page-editor"> \
                    <dl class="errordesc msg" style="display:none"> \
                        <dt>' + xg.page.nls.html('thereIsAProblem') + '</dt> \
                        <dd><ol></ol></dd> \
                    </dl> \
                    <fieldset class="fieldset" style="padding-left:0;"> \
                        <dl> \
                            <dt><label title="' + xg.page.nls.html('tabText') + '">' + xg.page.nls.html('tabTitle') + '</label></dt> \
                            <dd><input type="text" class="textfield" value="' + dojo.string.escape('html', title) + '" /></dd> \
                        </dl> \
                        <dl> \
                            <dt><label title="' + xg.page.nls.html('urlDirectory') + '">' + xg.page.nls.html('directory') + '</label></dt> \
                            <dd><input type="text" class="textfield" value="' + dojo.string.escape('html', directory) + '" /></dd> \
                            <dd><label title="' + xg.page.nls.html('displayTabForPage') + '"><input type="checkbox" class="checkbox" ' + (displayTab ? 'checked="checked"' : '') + ' />' + xg.page.nls.html('displayTab') + '</label> \
                        </dl> \
                        <ul class="actions"> \
                            <li><a href="#" class="delete desc">' + xg.page.nls.html('remove') + '</a></li> \
                            <li><a href="#" class="add desc">' + xg.page.nls.html('addAnotherPage') + '</a></li> \
                        </ul> \
                    </fieldset> \
                </div>';
        return dojo.dom.firstElement(container);
    },

    /**
     * Removes a page editor
     *
     * @param editor  the PageEditor element to remove
     */
    removePage: function(editor) {
        dojo.lfx.html.fadeOut(editor, 500, null, dojo.lang.hitch(this, function() {
            editor.parentNode.removeChild(editor);
        })).play();
    },

    /**
     * Returns the PageEditor widgets
     *
     * @param form  the form element
     * @return  the visible PageEditors
     */
    pageEditors: function(form) {
        var pageEditorDivs = dojo.html.getElementsByClass('page-editor', form, 'div');
        return dojo.lang.map(pageEditorDivs, function(div) { return dojo.widget.manager.getWidgetByNode(div); });
    }
};

(function() {
    var form = dojo.byId('xg_body').getElementsByTagName('form')[0];
    dojo.lang.forEach(dojo.json.evalJson(dojo.byId('data').value), function(instance) {
        xg.page.instance.edit.addPageProper(instance.title, instance.directory, instance.displayTab, false);
    });
    dojo.style.show(dojo.html.getElementsByClass('button', form, 'input')[0]);
    dojo.event.connect(form, 'onsubmit', function(event) {
        dojo.event.browser.stopEvent(event);
        if (dojo.style.isShowing(dojo.byId('spinner'))) { return; }
        dojo.style.show(dojo.byId('spinner'));
        dojo.lang.forEach(xg.page.instance.edit.pageEditors(), function(pageEditor) { pageEditor.hideErrors(); });
        dojo.io.bind({
            url: form.getAttribute('_url'),
            content: { data: dojo.json.serialize(dojo.lang.map(xg.page.instance.edit.pageEditors(), function(pageEditor) { return pageEditor.getData(); })) },
            method: 'post',
            encoding: 'utf-8',
            preventCache: true,
            mimetype: 'text/javascript',
            load: dojo.lang.hitch(this, function(type, data, event){
                dojo.style.hide(dojo.byId('spinner'));
                if (data.success) {
                    window.location = '/page/instance/edit?saved=1';
                    return;
                }
                var scrolled = false;
                var pageEditors = xg.page.instance.edit.pageEditors();
                for (i in data.errors) {
                    pageEditors[i].showErrors(data.errors[i]);
                    if (! scrolled) {
                        xg.index.util.FormHelper.scrollIntoView(pageEditors[i].domNode);
                        scrolled = true;
                    }
                }
            })
        });
    });

})();
