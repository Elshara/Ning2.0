<?php
$flashBody = '<embed src="' . xnhtmlentities($this->swfUrl) . '"
        quality="high" scale="noscale" salign="lt" wmode="transparent" bgcolor="#ffffff" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"
        width="' . $this->width . '"
        height="' . $this->height . '"
        allowScriptAccess="always"
        flashvars="' . xnhtmlentities(http_build_query($this->flashVars)) . '" />';
if ($this->internal) {
	$id = 'embedRenderDiv' . rand();
?>
<div id="<%=$id%>" style="width:<%= $this->width %>; height:<%= $this->height %>px;"></div>
<script type="text/javascript">
xg.addOnRequire(function() {
	setTimeout(function() {
		embedDiv = dojo.byId('<%=$id%>');
		embedDiv.innerHTML = <%=json_encode($flashBody)%>;
	},250);
});
</script>
<?php
} else {
	echo $flashBody;
}
if ($this->footerLinkUrl) { ?>
    <br /><small><a href="<%= xnhtmlentities($this->footerLinkUrl) %>"><%= $this->footerLinkHtml %></a></small><br />
<?php
}
?>
