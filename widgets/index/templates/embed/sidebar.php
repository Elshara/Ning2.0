<%= $this->sidebarNonCacheableHtml %>
<%= $this->sidebarSitewideHtml %>
<?php if (! $this->onlySitewide) { ?>
    <%= $this->sidebarNonSitewideHtml %>
<?php } ?>
