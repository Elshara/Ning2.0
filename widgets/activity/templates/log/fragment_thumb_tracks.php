<?php
/**
* @param $tracks
**/
?>
<div class="thumbs"> <?php
foreach($tracks as $track){
    if($track->my->enableProfileUsage){
        if(XG_SecurityHelper::userIsAdmin() || ($track->my->enableDownloadLink)){ ?>
        <a href="<%= xnhtmlentities($track->my->audioUrl) %>" class="play-button"><?php
        } else { ?>
        <a href="#" _href="<%= xnhtmlentities($track->my->audioUrl) %>" class="play-button"><?php
        }
        ?><img alt="<%= xg_html('PLAY') %>" src="<%= xg_cdn('/xn_resources/widgets/music/gfx/miniplayer.gif') %>" width="21" height="16"/></a>
        <%= xnhtmlentities($track->my->artist) %><%= ($track->my->artist && $track->my->trackTitle)?' &mdash;':'' %> <%= xnhtmlentities($track->my->trackTitle) %><br /><?php
    } else { ?>
        <img alt="<%= xg_html('PLAY') %>" src="<%= xg_cdn('/xn_resources/widgets/music/gfx/miniplayer.gif') %>" width="21" height="16" class="disabled" title="this song can only be played on the user page" />
        <a href="<%= xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($track->contributorName)) %>"><%=
        xnhtmlentities($track->my->artist) %><%= ($track->my->artist && $track->my->trackTitle)?' &mdash;':'' %> <%= xnhtmlentities($track->my->trackTitle) %></a><br /><?php
    } ?>
    <?php
}
?>
</div>