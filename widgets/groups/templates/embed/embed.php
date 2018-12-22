<?php
XG_App::addToCssSection('<link rel="stylesheet" type="text/css" media="screen,projection" href="'.xnhtmlentities(XG_Version::addXnVersionParameter($this->_widget->buildResourceUrl('css/module.css'))).'" />');
if (! $this->embed->isOwnedByCurrentUser()) { ?>
<div class="xg_module module_groups">
<?php
} else {
$json = new NF_JSON();
XG_App::ningLoaderRequire('xg.groups.embed.GroupModule'); ?>
<div class="xg_module module_groups" dojoType="GroupModule"
        _itemCount="<%= $this->embed->get('itemCount') %>"
        _setValuesUrl="<%= xnhtmlentities($this->setValuesUrl)%>"
        _updateEmbedUrl="<%= xnhtmlentities($this->updateEmbedUrl) %>"
        _optionsJson="<%= xnhtmlentities($json->encode($this->options))%>"
        _groupSet="<%= xnhtmlentities($this->embed->get('groupSet'))%>">
<?php
} ?>
    <div class="xg_module_head">
        <h2><%= xnhtmlentities($this->title) %></h2>
    </div>
    <?php $this->renderPartial('fragment_moduleBodyAndFooter', array('groups' => $this->groups, 'embed' => $this->embed, 'showViewAllLink' => $this->showViewAllLink, 'columnCount' => $this->columnCount, 'totalCount' => $this->totalCount)) ?>
</div>
