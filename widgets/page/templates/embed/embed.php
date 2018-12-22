<?php XG_App::addToCssSection('<link rel="stylesheet" type="text/css" media="screen,projection" href="'.xnhtmlentities(XG_Version::addXnVersionParameter($this->_widget->buildResourceUrl('css/module.css'))).'" />');
if (! $this->embed->isOwnedByCurrentUser()) { ?>
<div class="xg_module">
<?php
} else {
$json = new NF_JSON();
XG_App::ningLoaderRequire('xg.page.embed.PageModule'); ?>
<div class="xg_module" dojoType="PageModule"
        _setValuesUrl="<%= xnhtmlentities($this->setValuesUrl)%>"
        _optionsJson="<%= xnhtmlentities($json->encode($this->options))%>"
        _pageSet="<%= xnhtmlentities($this->embed->get('pageSet'))%>">
<?php
} ?>
    <div class="xg_module_head">
        <h2><%= xnhtmlentities($this->_widget->title) %></h2>
    </div>
    <?php $this->renderPartial('fragment_moduleBodyAndFooter', array('pagesAndComments' => $this->pagesAndComments, 'pages' => $this->pages, 'columnCount' => $this->columnCount, 'embed' => $this->embed, 'showContributorName' => $this->embed->getType() != 'profiles')) ?>
</div>
