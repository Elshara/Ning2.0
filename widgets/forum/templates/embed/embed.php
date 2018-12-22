<?php
if (!is_null($this->topicsAndComments)) {
    ob_start();
    $this->_widget->dispatch('embed', 'moduleBodyAndFooter', array(array('topicsAndComments' => $this->topicsAndComments, 'topics' => $this->topics, 'columnCount' => $this->columnCount, 'embed' => $this->embed, 'showContributorName' => $this->embed->getType() != 'profiles', 'feedAutoDiscoveryTitle' => $this->_widget->title, 'showFeedLink' => $this->showFeedLink, 'categoryIds' => $this->categories, 'userCanEdit' => $this->userCanEdit)));
    $moduleBodyAndFooter = trim(ob_get_contents());
    ob_end_clean();
} else {
    ob_start();
    $this->_widget->dispatch('embed', 'categories', array(array('categories' => $this->categories, 'showViewAll' => $this->categoryCount > $this->embed->get('itemCount'), 'embed' => $this->embed, 'userCanEdit' => $this->userCanEdit)));
    $moduleBodyAndFooter = trim(ob_get_contents());
    ob_end_clean();
}
if ($moduleBodyAndFooter) {
    XG_App::addToCssSection('<link rel="stylesheet" type="text/css" media="screen,projection" href="'.xnhtmlentities(XG_Version::addXnVersionParameter($this->_widget->buildResourceUrl('css/module.css'))).'" />');
    if (! $this->userCanEdit) { ?>
    <div class="xg_module module_forum">
    <?php
    } else {
    $json = new NF_JSON();
    XG_App::ningLoaderRequire('xg.forum.embed.ForumModule'); ?>
    <div class="xg_module module_forum" dojoType="ForumModule"
            _categoriesEnabled="<%= xnhtmlentities($this->categoriesEnabled) %>"
            _viewOptionsJson="<%= xnhtmlentities($json->encode($this->view_options)) %>"
            _viewSet="<%= xnhtmlentities($this->embed->get('viewSet'))%>"
            _numOptionsJson="<%= xnhtmlentities($json->encode($this->numOptions))%>"
            _itemCount="<%= $this->embed->get('itemCount') %>"
            _setValuesUrl="<%= xnhtmlentities($this->setValuesUrl)%>"
            _displayOptionsJson="<%= xnhtmlentities($json->encode($this->display_options)) %>"
            _displaySet="<%= xnhtmlentities($this->embed->get('displaySet'))%>"
            _optionsJson="<%= xnhtmlentities($json->encode($this->options))%>"
            _topicSet="<%= xnhtmlentities($this->embed->get('topicSet'))%>">
    <?php
    } ?>
        <div class="xg_module_head">
            <h2><%= xnhtmlentities($this->title) %></h2>
        </div>
        <%= $moduleBodyAndFooter %>
    </div>
<?php
} ?>
