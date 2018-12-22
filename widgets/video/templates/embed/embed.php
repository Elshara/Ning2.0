<?php XG_App::addToCssSection('<link rel="stylesheet" type="text/css" media="screen,projection" href="'.xnhtmlentities(XG_Version::addXnVersionParameter($this->_widget->buildResourceUrl('css/module.css'))).'" />');
if (! $this->embed->isOwnedByCurrentUser()) { ?>
<div class="xg_module module_video">
<?php
} else {
$json = new NF_JSON();
XG_App::ningLoaderRequire('xg.video.embed.embed'); ?>
<div class="xg_module module_video" dojotype="VideoModule"
    _setvaluesurl="<%= xnhtmlentities($this->setValuesUrl)%>"
    _videosetoptionsjson="<%= xnhtmlentities($json->encode($this->videoSetOptions))%>"
    _displaytype="<%= xnhtmlentities($this->embed->get('displayType'))%>"
    _videonum="<%= xnhtmlentities($this->embed->get('videoNum'))%>"
    _videoset="<%= xnhtmlentities($this->embed->get('videoSet'))%>"
    _numoptionsjson="<%= xnhtmlentities($json->encode($this->num_options))%>">
<?php
} ?>
    <div class="xg_module_head">
        <h2><%= xnhtmlentities($this->title) %></h2>
    </div>
    <?php $this->renderPartial('fragment_moduleBodyAndFooter', array('videos' => $this->videos, 'columnCount' => $this->columnCount,
                                'embed' => $this->embed, 'numVideos' => $this->numVideos)) ?>
</div>
