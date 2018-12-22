<?php XG_App::addToCssSection('<link rel="stylesheet" type="text/css" media="screen,projection" href="'.xnhtmlentities(XG_Version::addXnVersionParameter($this->_widget->buildResourceUrl('css/module.css'))).'" />');
$json = new NF_JSON();
if (! $this->embed->isOwnedByCurrentUser()) { ?>
<div class="xg_module module_photo">
<?php
} else {
XG_App::ningLoaderRequire('xg.photo.embed.PhotoModule');
?>
<div class="xg_module module_photo"
    dojotype="PhotoModule"
    _setValuesUrl="<%= xnhtmlentities($this->setValuesUrl)%>"
    _updateEmbedUrl="<%= xnhtmlentities($this->updateEmbedUrl) %>"
    _random="<%= $this->embed->get('random') ? 'true' : 'false' %>"
    _type="<%= xnhtmlentities($this->embed->get('photoType'))%>"
    _photoSet="<%= xnhtmlentities($this->embed->get('photoSet'))%>"
    _albumSet="<%= xnhtmlentities($this->embed->get('albumSet'))%>"
    _num="<%= xnhtmlentities($this->embed->get('photoNum'))%>"
    _typeOptions="<%= xnhtmlentities($json->encode($this->typeOptions))%>"
    _photoSetOptions="<%= xnhtmlentities($json->encode($this->photoSetOptions))%>"
    _albumSetOptions="<%= xnhtmlentities($json->encode($this->albumSetOptions))%>"
    _numOptions="<%= xnhtmlentities($json->encode($this->numOptions))%>">
<?php
} ?>
    <div class="xg_module_head">
        <h2><%= xnhtmlentities($this->title) %></h2>
    </div>
    <%= $this->moduleBodyAndFooterHtml %>
</div>
