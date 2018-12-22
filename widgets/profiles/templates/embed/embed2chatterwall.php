<?php XG_App::ningLoaderRequire('xg.profiles.embed.chatterwall','xg.shared.SimpleToolbar') ?>
<div class="no_cross_container xg_module xg_module_comment_wall"<?php
if ($this->embed->isOwnedByCurrentUser()) {
    XG_App::ningLoaderRequire('xg.profiles.embed.ChatterModule'); ?>
    dojoType="ChatterModule"
    isContainer="true"
    _url="<%= xnhtmlentities($this->embedUrl) %>"
    _moderate="<%= xnhtmlentities($this->userModeratesChatters) %>"
    _itemCount="<%= $this->embed->get('itemCount') %>"
<?php
} ?>>
    <?php $this->renderPartial('fragment_chatter_head', 'chatter', array('numComments' => $this->chatterInfo['numComments'])) ?>
    <div class="xg_module_body">
        <?php $this->renderPartial('fragment_chatter_add','chatter',array('profile' => $this->profile, 'isOwner' => $this->embed->isOwnedByCurrentUser())) ?>
    </div>
    <%= $this->moduleBodyAndFooterHtml %>
</div>
