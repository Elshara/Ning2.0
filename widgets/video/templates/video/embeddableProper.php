<embed
    src="<%= xnhtmlentities($this->swfUrl) %>"
    FlashVars="<%= xnhtmlentities(http_build_query($this->flashVars)) %>"
    width="<%= $this->width %>"
    height="<%= $this->height %>"
    scale="noscale"
    wmode="transparent"
    allowScriptAccess="always"
    allowFullScreen="true"
    type="application/x-shockwave-flash"
    pluginspage="http://www.macromedia.com/go/getflashplayer">
</embed>
<?php
if ($this->includeFooterLink) { ?>
    <br /><small><a href="<%= xnhtmlentities($this->_widget->buildUrl('video', 'index')) %>"><%= xg_html('FIND_MORE_VIDEOS_LIKE_THIS', preg_replace('/&#039;/u', "'", xnhtmlentities(XN_Application::load()->name))) %></a></small><br />
<?php
}
