<?php
if (XG_PromotionHelper::currentUserCanPromote($this->content)) {
    XG_App::ningLoaderRequire('xg.index.actionicons'); ?>
    <a dojoType="PromotionLink"
           _action="<%= $this->action %>"
           _type="<%= xnhtmlentities($this->type) %>"
           _id="<%= $this->content->id %>"
           _afterAction="<%= $this->afterAction %>"
           href="#" title="<%= $this->linkText %>" class="desc <%= $this->linkClass %>"><%= $this->linkText %></a>
<?php
} ?>

