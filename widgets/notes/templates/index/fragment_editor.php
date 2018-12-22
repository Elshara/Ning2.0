<?php
/*  $Id: $
 *
 *  Renders note editor
 *
 *  (all params see in edit.php)
 *
 */
$strings = array();
foreach (preg_split('/\s+/u',trim('TOGGLE_BOLD TOGGLE_ITALIC TOGGLE_UNDERLINE JUSTIFY_LEFT JUSTIFY_CENTER JUSTIFY_RIGHT TOGGLE_STRIKETHROUGH
    INDENT_LEFT INDENT_RIGHT INSERT_HR INSERT_OL INSERT_UL INSERT_IMAGE CREATE_NOTELINK CREATE_LINK REMOVE_LINK
    REMOVE_FORMATTING SIZE XXSMALL XSMALL SMALL MEDIUM LARGE XLARGE XXLARGE FONT
    NOTE_HAS_BEEN_CHANGED NOTE_TOO_LONG NOTE_TITLE_TOO_LONG
    ')) as $str)  {
    $strings[$str] = xg_text($str);
}
foreach(preg_split('/\s+/u',trim('YOU_ENTERED_INVALID_CHAR')) as $str) {
    $strings[$str] = xg_text($str);
}

?>
<script type="text/javascript">
// Mediator between GWT & our widgets(dojo).
window.notes = {
	activeTab: <%=intval($this->activeTab)%>,
    baseUrl: <%=json_encode(Notes_UrlHelper::noteUrl(NULL))%>,
    maxLength: <%=json_encode(Note::MAX_LENGTH)%>,
    maxTitleLength: <%=json_encode(Note::MAX_TITLE_LENGTH)%>,
    // "saved" note content. Used for tracking the fact of document change upon page unload. Updated when response come from server
    savedContent: <%=json_encode($this->noteContent)%>,
    // Current note version. Represents the version of note loaded into editor.
    currentVersion: <%=json_encode($this->noteVersion)%>,
    // component readiness: #0 - editor, #1 - widget
    _ready: [0,0],
    componentIsReady: function(component) {
        if (window.notes._ready[component]) { return }
        window.notes._ready[component] = 1;
        if (window.notes._ready[component ? 0 : 1]) { window.notes.widgetRun() }
    },

    // defined by widget
    widgetRun: undefined,
    widgetInsertImage: undefined,
    widgetInsertLink: undefined,
    widgetInsertNoteLink: undefined,

    // defined by GWT
    editorGetText: undefined,
    editorSetText: undefined,
    editorInsertImage: undefined,
    editorCreateLink: undefined,
    editorDisableToolbar: undefined,
    editorEnableToolbar: undefined
};
var notesStrings = <%=json_encode($strings)%>;
</script>
<?php if ($this->isMain) {?>
    <div class="inplace_edit">
      <input id="noteTitle" class="notes_title textfield h1" type="text" size="50" value="<%=xnhtmlentities($this->title)%>"/>
    </div>
<?php } else {
	echo xg_headline($this->title);
}?>
<div class="xg_module">
    <div class="xg_module_body" style="position:relative">
        <form method="post" action="" dojoType="NoteEditor" _saveUrl="<%=xnhtmlentities(Notes_UrlHelper::noteUrl($this->noteKey,'update'))%>" _cancelUrl="<%=xnhtmlentities(Notes_UrlHelper::noteUrl($this->noteKey))%>">
            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
            <p style="z-index:2;position:absolute;right:5px;top:5px">
                <img id="noteSpinner1" src="<%= xg_cdn('/xn_resources/widgets/index/gfx/icon/spinner.gif') %>" style="display:none"/>
                <input type="button" class="button button-primary" name="save1" style="display:none" value="<%=xg_html('SAVE')%>"/>
                <input type="button" class="button" name="cancel1" style="display:none" value="<%=xg_html('CANCEL')%>"/>
            </p>
            <ul id="noteTabs" class="page_tabs" style="margin-bottom:0; display:none">
                <li id="noteEditorTab"><span class="xg_tabs"><%=xg_html('RICH_TEXT')%></span></li>
                <li id="noteSourceTab"><span class="xg_tabs"><%=xg_html('SOURCE')%></span></li>
                <li id="notePreviewTab"><span class="xg_tabs"><%=xg_html('PREVIEW')%></span></li>
            </ul>
            <div id="noteEditorToolbarWrapper" class="gwteditor easyclear clear" style="border:1px solid #999; border-width:0 1px 1px; border-bottom-color:#ccc; display:none">
                <div id="noteEditorToolbarLock" style="position:absolute;background-color:#FFFFFF; opacity:0.6; filter:alpha(opacity=60);display:none"><div></div></div>
                <div id="noteEditorToolbar" class="richeditor"></div>
            </div>
            <div id="noteStub"><img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/spinner.gif') %>" /></div>
            <div id="noteEditor" class="notes_body_edit" style="display:none"></div>
            <div id="noteSource" class="notes_body_edit" style="display:none"><textarea name="sourceText" style="width:733px;height:30em; border:none"></textarea></div>
            <div id="notePreview" class="notes_body" style="display:none"></div>
            <p class="small" id="noteSaveMessage">
                <%=xg_html('NOTES_QUICK_HELP')%>
            </p>
            <p class="buttongroup last-child">
                <img id="noteSpinner2" src="<%= xg_cdn('/xn_resources/widgets/index/gfx/icon/spinner.gif') %>" style="display:none"/>
                <input type="button" class="button button-primary" name="save2" style="display:none" value="<%=xg_html('SAVE')%>"/>
                <input type="button" class="button" name="cancel2" style="display:none" value="<%=xg_html('CANCEL')%>"/>
            </p>
        </form>
    </div>
</div>
<div id="noteSaveError" class="xg_floating_module" style="display:none">
    <div class="xg_floating_container xg_module" style="width:450px; left: -225px">
        <div class="xg_module_head">
            <h2><%=xg_html('CANNOT_SAVE_NOTE')%></h2>
        </div>
        <div class="xg_module_body">
            <form>
                <p class="normal label1"></p>
                <p class="buttongroup" style="display: none">
                    <input type="button" class="button" name="overwrite" value="<%=xg_html('SAVE')%>" />
                    <input type="button" class="button" name="discard" value="<%=xg_html('DISCARD')%>" />
                    <input type="button" class="button" name="cancel" value="<%=xg_html('CANCEL')%>" />
                </p>
                <p class="buttongroup" style="display: none">
                    <input type="button" class="button" name="recreate" value="<%=xg_html('SAVE')%>" />
                    <input type="button" class="button" name="discard2" value="<%=xg_html('DISCARD')%>" />
                    <input type="button" class="button" name="cancel2" value="<%=xg_html('CANCEL')%>" />
                </p>
                <p class="buttongroup" style="display: none">
                    <input type="button" class="button" name="ok" value="<%=xg_html('OK')%>" />
                </p>
            </form>
        </div>
    </div>
</div>
<div id="noteCreateLink" class="xg_floating_module" style="display:none">
    <div class="xg_floating_container">
        <div class="xg_module_head">
            <h2><%=xg_html('CREATE_LINK')%></h2>
        </div>
        <div class="xg_module_body">
            <form onsubmit="this.ok.onclick();return false">
                <p class="normal">
                    <label for=""><%=xg_html('ENTER_A_LINK_URL')%><br /><input class="textfield" type="text" value="" style="width:98%" name="url"/></label>
                </p>
                <p class="buttongroup">
                    <input type="button" class="button" name="ok" value="<%=xg_html('OK')%>" />
                    <input type="button" class="button" name="cancel" value="<%=xg_html('CANCEL')%>" />
                </p>
            </form>
        </div>
    </div>
</div>
<div id="noteCreateNoteLink" class="xg_floating_module" style="display:none">
    <div class="xg_floating_container">
        <div class="xg_module_head">
            <h2><%=xg_html('CREATE_NOTELINK')%></h2>
        </div>
        <div class="xg_module_body">
            <form onsubmit="this.ok.onclick();return false">
                <p class="normal">
                    <label for=""><%=xg_html('ENTER_THE_NOTE_TITLE')%><br /><input class="textfield" type="text" value="" style="width:98%"  name="title"/></label><br />
                </p>
                <p class="buttongroup">
                    <input type="button" class="button button-primary" name="ok" value="<%=xg_html('OK')%>" />
                    <input type="button" class="button" name="cancel" value="<%=xg_html('CANCEL')%>" />
                </p>
            </form>
        </div>
    </div>
</div>
