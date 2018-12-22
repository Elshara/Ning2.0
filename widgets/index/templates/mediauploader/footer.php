<?php
XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
XG_App::ningLoaderRequire('xg.index.mediauploader.container');
ob_start(); ?>
<applet code="ning.uploader.UploaderApplet" archive="<%= xnhtmlentities($this->appletUrl) %>" width="660" height="440" mayscript="true">
    <param name="uploader-type" value="<%= xnhtmlentities(mb_strtoupper($this->type)) %>">
    <param name="upload-url" value="<%= xnhtmlentities($this->uploadUrl) %>">
    <param name="success-url" value="<%= xnhtmlentities($this->successUrl) %>">
    <param name="max-items" value="<%= xnhtmlentities($this->maxItems) %>">
    <?php if ($this->showDisabledFiles) { ?><param name="show-disabled-files" value="true"><?php } ?>
    <?php if ($this->fileBrowserType) { ?><param name="file-browser-type" value="<%= xnhtmlentities($this->fileBrowserType) %>"><?php } ?>
    <?php if ($this->disableMusicDownload) { ?><param name="download-enabled" value="true"/><?php } ?>
    <?php /* TODO: Create a $this->disableMusicDownload variable, for consistency [Jon Aquino 2008-01-23] */ ?>
</applet>
<?php
$output = str_replace("\n", " ", ob_get_contents());
ob_end_clean(); ?>
<input id="uploader_html" type="hidden" value="<%= xnhtmlentities($output) %>" />
<?php
// Two reasons to create the applet using JavaScript: (1) prevent IE's "Click to activate" message
// (2) ensure JavaScript I18N strings are loaded first  [Jon Aquino 2007-12-17]
?>
<script src="<%= xg_cdn('/xn_resources/widgets/shared/js/setInnerHtmlFromExternalScript.js') %>"></script>
<script src="<%= xg_cdn('/xn_resources/widgets/shared/js/PluginDetect.js') %>"></script>
