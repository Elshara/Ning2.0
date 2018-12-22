<?php
$isProfile      = ($this->embed->getType() == 'profiles');
$no_activity    = ($this->embed->get('activityItemsCount') == 0);
$isEmbedOwner   = ($this->embed->getOwnerName() == XN_Profile::current()->screenName);
?>
<?php
if ($isProfile && $no_activity && !$isEmbedOwner) {
	return;
}
if ($this->activity_off_network){ ?>
    <div class="xg_module_body">
    <p><%= xg_html('THE_NETWORK_CREATOR_HAS_TURNED_OFF_THE_ACTIVITY')%></p>
    </div><?php
} else if ($this->activity_off_user){ ?>
    <div class="xg_module_body">
    <p><%= xg_html('YOUVE_TURNED_OFF_THE_ACTIVITY_DISPLAY_CLICK', 'href="' . xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('profile', 'privacySettings')) . '"')%></p>
    </div><?php
} else if($this->embed->get('activityNum')==0){ 
    // display nothing
} else if ($no_activity) {
    if ($isProfile) { ?>
    <div class="xg_module_body">
    <p><%= xg_html('YOU_HAVENT_DONE_ANYTHING_RECENTLY')%></p>
    </div><?php
    } else { ?>
    <div class="xg_module_body">
    <p><%= xg_html('NOTHING_IS_HAPPENING_ON_YOUR_NETWORK')%></p>
    </div>
    <?php
    }
} else {?>
    <div class="xg_module_body">
        <?php 
        $activityCount = count($this->logItems);
        $counter = 1;
        foreach($this->logItems as $item){
            $this->renderPartial('fragment_logItem', 'log', array('item' => $item, 
                                                                'isProfile' => $isProfile, 
                                                                'profileOwner' => $this->embed->getOwnerName(),
                                                                'lastChild' => $activityCount == $counter));
            $counter ++;
        } ?>
    </div>
<?php } ?>
    <?php
if((! XG_App::appIsPrivate())||($isProfile)){ ?>
    <div class="xg_module_foot">
        <ul><?php
    if(! XG_App::appIsPrivate()){
        $rssUrl = $this->_buildUrl('log', 'list', array('fmt'=>'rss', 'screenName' => (($isProfile)?$this->embed->getOwnerName():null) ));
        xg_autodiscovery_link($rssUrl, $this->rssTitle);  ?>
            <li class="left"><a class="desc rss" href="<%= xnhtmlentities($rssUrl) %>"><%= xg_html('RSS')%></a></li>
<?php
    }
    if (($isProfile)&&($isEmbedOwner)) { ?>
            <li class="right"><a href="<%= xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('profile', 'privacySettings')) %>"><%= xg_html('PRIVACY')%></a></li>
<?php } ?>
        </ul>
    </div><?php
}
