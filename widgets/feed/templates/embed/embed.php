<?php XG_App::ningLoaderRequire('xg.feed.embed.embed'); ?>
<?php
if ($this->userCanEdit) { ?>
<div class="xg_module module_feed xg_reset"
        dojoType="FeedModule"
        itemCount="<%= $this->itemCount %>"
        showDescriptions="<%= $this->showDescriptions %>"
        feedUrl="<%= xnhtmlentities($this->feedUrl) %>"
        updateEmbedUrl="<%= xnhtmlentities($this->_buildUrl('embed', 'updateEmbed', array('id' => $this->embed->getLocator(), 'xn_out' => 'json'))) %>"
        setValuesUrl="<%= xnhtmlentities($this->_buildUrl('embed', 'setValues', array('id' => $this->embed->getLocator(), 'xn_out' => 'json', 'maxEmbedWidth' => $this->maxEmbedWidth, 'sidebar' => XG_App::isSidebarRendering() ? '1' : '0'))) %>">
<?php
} else { ?>
<div class="xg_module module_feed xg_reset">
<?php
} ?>
    <div class="xg_module_head"><h2><%= xnhtmlentities($this->title) %></h2></div>
    <?php $this->renderPartial('fragment_moduleBodyAndFooter', array('feedUrl' => $this->feedUrl, 'itemCount' => $this->itemCount, 'showDescriptions' => $this->showDescriptions, 'maxEmbedWidth' => $this->maxEmbedWidth)) ?>
</div>
