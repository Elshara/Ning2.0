<?php
$isProfile      = ($this->embed->getType() == 'profiles');
$isAdmin        = XG_SecurityHelper::userIsAdmin();
$isEmbedOwner   = ($this->embed->getOwnerName() == XN_Profile::current()->screenName);
$no_activity    = ($this->embed->get('activityItemsCount') == 0);

if(($isEmbedOwner)||(XG_SecurityHelper::userIsAdmin())||( ((!$this->activity_off_network) && (!$this->activity_off_user) && (!$no_activity) && ($this->embed->get('activityNum')>0)))){
?><div class="xg_module xg_module_activity <%= ($isProfile) ?'profile':''; %>  column_<%=($this->columnCount)%>"
    dojotype="ActivityModule"
    _isProfile="<%= $isProfile %>"
    _isAdmin="<%= $isAdmin %>"

    <?php /* TODO: Move the non-URL _del* attributes into the ActivityModule widget [Jon Aquino 2007-09-05] */ ?>
    _delConfirmTitle="<%= xg_html('DELETE_ACTIVITY_ITEM') %>"
    _delConfirmQuestion="<%= xg_html('DELETE_THIS_ACTIVITY_MESSAGE_Q') %>"
    _delConfirmOk="<%= xg_html('OK') %>"
    _delDeleteLinkText="<%= xg_html('DELETE') %>"
    _delItemUrl="<%= xnhtmlentities(W_Cache::getWidget('activity')->buildUrl('log','remove'))%>"
    _delIconTooltip="<%= xg_html('REMOVE') %>" <?php

if ($this->embed->isOwnedByCurrentUser()) {
	XG_App::ningLoaderRequire('xg.shared.EditUtil');
    $json = new NF_JSON(); ?>
        _numoptionsjson="<%= xnhtmlentities($json->encode($this->num_options))%>"
        _activitynum="<%= xnhtmlentities($this->embed->get('activityNum'))%>"
        _setValuesUrl="<%= xnhtmlentities($this->setValuesUrl)%>"
        _settingsUrl="<%= xnhtmlentities(W_Cache::getWidget('main')->buildUrl('activity', 'edit'))%>" <?php
} ?> >
    <div class="xg_module_head">
        <h2><%= xg_html('LATEST_ACTIVITY') %></h2>
    </div>
    <%= $this->moduleBodyAndFooterHtml %>
</div>
<?php
XG_App::ningLoaderRequire('xg.activity.embed.embed');
//@TODO only call if there is music tracks in the activity list
XG_App::ningLoaderRequire('xg.music.shared.buttonplayer');
}
?>
