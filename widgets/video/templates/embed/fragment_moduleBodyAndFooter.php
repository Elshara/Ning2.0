<?php
/** TODO: docs [Thomas David Baker 2007-03-28]
 * @param $videos
 * @param $columnCount
 * @param $embed
 * @param $numVideos
 */
?>
<?php
XG_App::includeFileOnce('/lib/XG_FacebookHelper.php');
if (count($videos) && $embed->get('videoNum') != 0) {
?>
<div class="xg_module_body body_large">
  <ul class="clist">
  <?php
  $i = 0;
  foreach($videos as $video) { ?>
    <li>
        <div class="ib">
            <?php $this->renderPartial('fragment_thumbnailProper', 'video', array('video' => $video, 'thumbWidth' => 136)); ?>
        </div>
        <div class="tb">
            <h3><a href="<%= xnhtmlentities($this->_buildUrl('video', 'show', array('id' => $video->id))) %>">
                <%= xnhtmlentities($video->title) %>
            </a></h3>
            <?php $commentCounts = Comment::getCounts($video) ?>
            <p class="xg_lightfont"><%= xg_html('ADDED_BY_X', Video_HtmlHelper::linkedScreenName($video->contributorName, false, true, 'xg_lightfont')) %>
            </p>
            <?php if ($video->my->ratingAverage > 0) { ?>
               <%= Video_HtmlHelper::stars($video->my->ratingAverage) %>
            <?php } ?>
        </div>
    </li>
    <?php
      $i++;
      if ($columnCount > 1 && $i % 3 == 0) { echo '</ul><ul class="clist">'; } //BAZ-6027
    }
    ?>
  </ul>
</div>
<?php }
if ($numVideos && $embed->get('videoNum') == 0 && $embed->isOwnedByCurrentUser()) { ?>
    <div class="xg_module_foot"><ul>
        <li class="left"><a class="add desc" href="<%= xnhtmlentities($this->_buildUrl('video', XG_MediaUploaderHelper::action())) %>"><%= xg_html('ADD_A_VIDEO') %></a></li>
    </ul></div>
<?php
} else if (! $numVideos && $embed->get('videoSet') == 'promoted' && $embed->isOwnedByCurrentUser()) { ?>
    <div class="xg_module_body">
        <h3><%= xg_html('THERE_ARE_NO_FEATURED_X', mb_strtolower('VIDEOS')) %></h3>
        <p><%= xg_html('START_FEATURING_X_CLICK_Y', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildRelativeUrl('admin','featuring')) .'"') %></p>
    </div>
<?php
} else if (! $numVideos && ($embed->get('videoSet') == 'all' || $embed->get('videoSet') == 'rated') && $embed->isOwnedByCurrentUser()) { ?>
    <div class="xg_module_body">
        <p class="last-child"><a <%= XG_JoinPromptHelper::promptToJoin($this->_buildUrl('video', XG_MediaUploaderHelper::action())) %> class="desc add"><%= xg_html('ADD_A_VIDEO') %></a></p>
    </div>
<?php
} else if (count($videos)) { 
    $viewAllUrl = $embed->getType() == 'profiles' ? $this->_buildUrl('video', 'listForContributor', array('screenName' => $embed->getOwnerName())) : $this->_buildUrl('video', 'index');
    if (XG_FacebookHelper::isAppEnabled('video')) { ?>
        <div class="xg_module_body xg_module_facebook">
            <p class="left">
                <small><img src="<%= qh(xg_cdn('/xn_resources/widgets/index/gfx/icon/facebook.gif')) %>"><a href="<%= qh(XG_FacebookHelper::getFacebookEmbedAppUrl('video')) %>"><%= xg_html('FACEBOOK_ADD_TO_FACEBOOK') %></a></small>
            </p>
            <p class="right"><small><a href="<%= xnhtmlentities($viewAllUrl) %>"><%= xg_html('VIEW_ALL') %></a></small></p>
        </div>
    <?php 
    } else { ?>
        <div class="xg_module_foot">
            <ul>
                <?php if (Video_SecurityHelper::checkCurrentUserCanAddVideos(XN_Profile::current()) == NULL) { ?>
                    <li class="left"><a class="add desc" href="<%= xnhtmlentities($this->_buildUrl('video', XG_MediaUploaderHelper::action())) %>"><%= xg_html('ADD_A_VIDEO') %></a></li>
                <?php } ?>
                <li class="right"><a href="<%= xnhtmlentities($viewAllUrl) %>"><%= xg_html('VIEW_ALL') %></a></li>
            </ul>
        </div>
    <?php 
    }
} ?>
