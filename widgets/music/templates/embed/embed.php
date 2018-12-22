<?php XG_App::addToCssSection('<link rel="stylesheet" type="text/css" media="screen,projection" href="'.xnhtmlentities(XG_Version::addXnVersionParameter($this->_widget->buildResourceUrl('css/module.css'))).'" />');
if (! $this->embed->isOwnedByCurrentUser()) {
    if (($this->trackCount>0)||($this->playlistSet=='podcast')){
        ?>
<div class="xg_module music xg_module_music column_<%=($this->columnCount)%>">
    <?php $this->renderPartial('fragment_moduleBodyAndFooter', array('columnCount' => $this->columnCount)) ?>
</div>
<?php
    }
} else {
	XG_App::ningLoaderRequire('xg.music.embed.MusicModule');
    $json = new NF_JSON();
     ?>
    <div class="xg_module music can_edit column_<%=($this->columnCount)%>" dojotype="MusicModule"
            _setvaluesurl="<%= xnhtmlentities($this->setValuesUrl)%>"
            _playlistoptionsjson="<%= xnhtmlentities($json->encode($this->playlist_options))%>"
            _autoplay="<%= xnhtmlentities($this->embed->get('autoplay'))%>"
            _shuffle="<%= xnhtmlentities($this->embed->get('shuffle'))%>"
            _showplaylist="<%= xnhtmlentities($this->embed->get('showPlaylist'))%>"
            _playlistset="<%= xnhtmlentities($this->embed->get('playlistSet'))%>"
            _playlisturl="<%= ($this->playlistSet=='podcast')?xnhtmlentities($this->embed->get('playlistUrl')):'' %>"
            _columncount="<%= xnhtmlentities($this->columnCount)%>"
            >
    <div class="xg_module_head">
        <h2><%= xnhtmlentities($this->title) %></h2>
    </div>
    <?php $this->renderPartial('fragment_moduleBodyAndFooter', array('columnCount' => $this->columnCount)) ?>
</div>
<?php
} ?>
