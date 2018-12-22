<?php
if ($this->_user->isLoggedIn() && ! $_GET['test_signed_out']) { $signedInUser = Video_UserHelper::load($this->_user); }
XG_App::includeFileOnce('/lib/XG_MetatagHelper.php');
XG_App::includeFileOnce('/lib/XG_ShareHelper.php');
XG_App::ningLoaderRequire('xg.shared.FavoriteLink');
$signup_share_target = XG_HttpHelper::addParameters(XG_HttpHelper::currentUrl(), array('open_share_dialog'=>'yes'));
$signup_favorize_link = XG_HttpHelper::addParameters($this->_buildUrl('favorite', 'create', '?videoId=' . $this->video->id), array('after'=>$this->afterLoginToken));
$swfUrl = xg_cdn($this->_widget->buildResourceUrl('flvplayer/flvplayer.swf'));
preg_match('@FlashVars="([^"]*)@u', $this->embedCode, $matches);
$flashVars = $matches[1];
preg_match('@width="([^"]*)@u', $this->embedCode, $matches);
$videoWidth = $matches[1];
preg_match('@height="([^"]*)@u', $this->embedCode, $matches);
$videoHeight = $matches[1];
$videoWithPlayer = $swfUrl.(mb_strpos($swfUrl,'?')?'&':'?').$flashVars;
$disableOthers = file_exists(NF_APP_BASE . "/xn_private/disable_others") || file_exists(NF_APP_BASE . "/lib/disable_others");
$isNing = (mb_strpos($this->embedCode, XN_AtomHelper::HOST_APP('static'))>0);

XG_App::addToSection('<link rel="videothumbnail" href="' . Video_VideoHelper::previewFrameUrl($this->video) . '" />');
XG_App::addToSection('<link rel="image_src" href="' . Video_VideoHelper::previewFrameUrl($this->video) . '" />');
XG_App::addToSection('<meta name="title" content="' . xnhtmlentities(($title = $this->video->title) ? $title : xg_text('VIDEO_BY_X', Video_FullNameHelper::fullName($this->video->contributorName))) .'" />');
XG_App::addToSection('<link rel="video_src" href="' . $videoWithPlayer . '" />
<meta name="video_type" content="application/x-shockwave-flash" />');
XG_App::addToSection('<meta name="video_width" content="' . $videoWidth . '" /> ');
XG_App::addToSection('<meta name="video_height" content="' . $videoHeight . '" /> ');

xg_header(W_Cache::current('W_Widget')->dir, ($title = $this->video->title) ? $title : xg_text('VIDEO_BY_X', Video_FullNameHelper::fullName($this->video->contributorName)), NULL, array(
	'metaKeywords' => $this->video->my->topTags,
	'metaDescription' => $this->video->description,
	'showFacebookMeta' => $this->showFacebookMeta,
	'facebookPreviewImage' => Video_VideoHelper::previewFrameUrl($this->video),
));
if (! is_null($this->commentFeedUrl)) {
    xg_autodiscovery_link($this->commentFeedUrl, xg_text('COMMENTS_TITLE', $this->video->title), 'atom');
}
if ($this->embedWidth) {
    $outerDivStyle = "style='width:" . $this->embedWidth . "px;'";
}
if ((!$disableOthers)&&($isNing)) {
    W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_ClearspringHelper.php');
}
?>
<div id="xg_body">
<?php
if (Video_VideoHelper::isAwaitingApproval($this->video)) { ?>
    <div class="xg_module">
        <div class="xg_module_body notification topmsg">
            <p style="line-height: 1.8em;" class="last-child">
                <%= xg_html('VIDEO_IS_WAITING_FOR_APPROVAL', xnhtmlentities(XN_Application::load()->name)) %>
            </p>
        </div>
    </div>
<?php
} ?>
    <div class="xg_column xg_span-16">
		<?php XG_PageHelper::subMenu(Video_HtmlHelper::subMenu($this->_widget)) ?>
        <div class="xg_module xg_module_with_dialog">
			<%= xg_headline($this->video->title, array(
					'avatarUser' => XG_Cache::profiles($this->video->contributorName),
					'byline1Html' => xg_html('ADDED_BY_X_AT_X',
                                'href="' . xnhtmlentities(User::quickProfileUrl($this->video->contributorName)).'"',
                                xnhtmlentities(XG_FullNameHelper::fullName($this->video->contributorName)),
                                xg_date(xg_text('G_IA'), strtotime($this->video->createdDate)),
                                xg_date(xg_text('F_JS_Y'),strtotime($this->video->createdDate))),
					'byline2Html' => xg_message_and_friend_links($this->video->contributorName, $this->_buildUrl('video', 'listForContributor', array('screenName' => $this->video->contributorName)), xg_text('VIEW_VIDEOS')),
			)) %>
            <div class="xg_module_body nopad">
                <div class="vid_container" <%= $outerDivStyle %>>
                    <div <%= $outerDivStyle %>>
                    <?php

                    // Wait for onLoad before showing the player, for smoother playback (i.e. wait for
                    // scripts to finish).  [Jon Aquino 2006-09-16]
                    // Insert the player from a separate file, to avoid the two-clicks in IE
                    // required by the Eolas patent dispute. See "Activating ActiveX Controls",
                    // http://msdn.microsoft.com/library/?url=/workshop/author/dhtml/overview/activating_activex.asp  [Jon Aquino 2006-09-16]
                    if ($this->video->my->conversionStatus == 'complete' || $this->video->my->embedCode) { ?>
                        <p class="loading" <%= $outerDivStyle %>><%= xg_html('LOADING') %></p>
                        <input type="hidden" id="playerHtml" <%= $this->video->my->embedCode ? '_thirdParty="true"' : '' %> value="<?php echo xnhtmlentities(preg_replace('/\s+/u', ' ', $this->playerHtml)) ?>" />
                    <?php
                    } else {
                        echo $this->playerHtml;
                        echo '<div id="convPlaceHolder" style="display:none;"></div>';
                    } ?>
                    </div>
                    <?php if ($this->video->description) { ?>
                    <p class="description"><%= xg_nl2br(xg_resize_embeds($this->video->description, 737)) %></p>
                    <?php }
                    ?>
                    <div class="edit_options">
                        <?php
                        if ($this->_user->isLoggedIn()) {
                            $rating = Video_UserHelper::getRating($signedInUser, $this->video->id);
                            $setRatingUrl = $this->_buildUrl('rating', 'update', '?videoId=' . $this->video->id); ?>
                            <small class="left"><%= xg_html('RATE_COLON') %>&nbsp;</small> <%= xg_rating_widget($rating, $setRatingUrl, 'xj_video_rating') %> <br/>
                        <?php
                        } ?>
                        <?php if ($this->video->my->visibility == 'all' && ($this->video->my->conversionStatus == 'complete'
                                                        || $this->video->my->embedCode)) {
                        $app = XN_Application::load();
                        $this->appName = $app->name;
                        $this->appUrl = 'http://' . $_SERVER['HTTP_HOST'];
                        $myspaceUrl = XG_ShareHelper::postToMyspaceUrl($title, $this->embedCode, $this->appUrl, 5);
                        $facebookUrl = XG_ShareHelper::postToFacebookUrl($this->_buildUrl('video', 'show', '?id=' . $this->video->id), $title);
?>
                        <a class="desc embed" href="#"
                               dojoType="ShowEmbedToggle"
                               _toOpenText="<%= xg_html('GET_EMBED_CODE') %>"
                               _toCloseText="<%= xg_html('HIDE_EMBED_CODE') %>"
                               _directURL="<%= xnhtmlentities($this->_buildUrl('video', 'show', '?id=' . $this->video->id)) %>"
                               _embedCode="<%= xnhtmlentities($this->embedCode) %>"
                               _myspacePostUrl="<%= $myspaceUrl %>"
                               _facebookPostUrl="<%= $facebookUrl %>"
                               <?php if ((!$disableOthers)&&($isNing)) {
                                   $config = Index_ClearspringHelper::extractConfigJson($this->embedCode, '&href='.$this->appUrl. '&networkName='. $this->appName);
                                   ?>
                               _othersCustomCSS="<%= Index_ClearspringHelper::getClearspringCssUrl() %>"
                               _config="<%= xnhtmlentities($config) %>"
                               <?php } ?>
                               _disableOthers="<%= $disableOthers %>"
                               _isNing="<%= $isNing %>"
                               _widgetId="483eff9c876f62ff"
                               ><%= xg_html('GET_EMBED_CODE') %></a><br/>
                        <?php } ?>
                        <?php
                        if (XG_SecurityHelper::userIsAdminOrContributor($this->_user,$this->video)) {
                            XG_App::ningLoaderRequire('xg.index.bulk', 'xg.shared.TagLink');
                            $addOrEdit = mb_strlen($this->currentUserTagString) ? 'edit' : 'add';
                            if (XG_SecurityHelper::userIsContributor($this->_user,$this->video)) { ?>
                                <a class="desc edit" href="<%= xnhtmlentities($this->_buildUrl('video', 'edit', '?id=' . $this->video->id)) %>"><%= xg_html('EDIT_VIDEO') %></a><br/>
                            <?php } ?>
                            <span dojoType="TagLink"
                                _actionUrl="<%= xnhtmlentities($this->_buildUrl('video', 'tag', array('id' => $this->video->id, 'xn_out' => json))); %>"
                                _popOver="true"
                                _tags="<%= xnhtmlentities($this->currentUserTagString); %>"">
                                <a class="desc <%= $addOrEdit %>" href="#"><%= $addOrEdit == 'edit' ? xg_text('EDIT_YOUR_TAGS') : xg_text('ADD_TAGS') %></a>
                            </span><br/>
                            <a class="desc delete" href="#"
                                    dojoType="BulkActionLink"
                                    title ="<%= xg_html('DELETE_THIS_VIDEO_Q') %>"
                                    _confirmMessage ="<%= xg_html('ARE_YOU_SURE_DELETE_THIS_VIDEO', xnhtmlentities(XG_FullNameHelper::fullName($signedInUser->title))) %>"
                                    _url = "<%= xnhtmlentities($this->_buildUrl('bulk', 'remove', array('limit' => 20, 'id' => $this->video->id, 'xn_out' => 'json'))) %>"
                                    _successUrl = "<%= $this->_buildUrl('video', 'index') %>"
                                    _progressTitle = "<%= xg_html('DELETING') %>"
                                    ><%= xg_html('DELETE_VIDEO') %></a><br/>
                        <?php
                        } ?>
                    </div>
                    <p class="small">
                        <%= xg_html('RATING') %>
						<span id="xj_video_rating"><%= xg_rating_image($this->video->my->ratingAverage) %></span><br/>
                        <?php
                        if ($this->tags) {
                            $tagUrl = W_Cache::getWidget('video')->buildUrl('video', 'listTagged'); ?>
                            <span id="tagsList"><%= xg_html('TAGS') %> <%= xg_tag_links($this->tags, $tagUrl, 5, true) %></span><br/>
                        <?php
                        } ?>
                        <?php if ($this->video->my->conversionStatus != 'in progress') { ?>
                            <%= xg_html('VIEWS') %> <%= xnhtmlentities($this->video->my->viewCount) %><br/>
                        <?php } ?>
                        <?php
                        if ($this->video->my->favoritedCount) { ?>
                            <%= xg_html('FAVORITE_OF_N_PEOPLE', $this->video->my->favoritedCount) %><br/>
                        <?php
                        }
                        $locationLinks = xg_location_links($this->video->my->lat, $this->video->my->lng, $this->video->my->locationInfo, $this->video->my->location, $this->_buildUrl('video', 'listForLocation', array('location' => $this->video->my->location)), $mapDiv);
                        if ($locationLinks) { ?>
                            <%= xg_html('LOCATION_COLON') %> <%= $locationLinks %><br/>
                        <?php
                        } ?>
                     </p>
                     <%= $mapDiv %>
                     <p class="clear">
                         <?php
                         if (Video_PrivacyHelper::canCurrentUserSeeShareLinks($this->video)) {
                             $shareUrl = W_Cache::getWidget('main')->buildUrl('sharing', 'share', array(
                                 'id' => urlencode($this->video->id),
                             )); ?>
                             <a class="desc share" href="<%= $shareUrl %>"><%= xg_text('SHARE') %></a> &nbsp;
                         <?php
                         } ?>
                          <?php if ($signedInUser) { ?>
                              <a dojotype="FavoriteLink" class="desc <%= Video_UserHelper::hasFavorite($signedInUser, $this->video->id) ? 'favorite-remove' : 'favorite-add' %>"  href="#"
                              _addurl="<%= $this->_buildUrl('favorite', 'create', '?videoId=' . $this->video->id); %>"
                              _removeUrl="<%= $this->_buildUrl('favorite', 'delete', '?videoId=' . $this->video->id); %>"
                              _hasFavorite="<%= Video_UserHelper::hasFavorite($signedInUser, $this->video->id) %>"
                              ><%= Video_UserHelper::hasFavorite($signedInUser, $this->video->id) ? xg_html('REMOVE_FROM_FAVORITES') : xg_html('ADD_TO_FAVORITES') %></a> &nbsp;
                         <?php } ?>
                          <?php
                              W_Cache::getWidget('main')->dispatch('promotion','link',array($this->video,'video'));
                          ?>
                    </p>
                    </div>
                    <?php if ($this->showRelated) {
                        $this->renderPartial('fragment_related', 'video', array('videos'=>$this->relatedVideos, 'title'=>$this->relatedTitle));
                    }
                    ?>
                </div>
            </div>
            <?php
            $commentData = array();
            foreach ($this->comments as $comment) {
                $commentData[] = array(
                    'comment' => $comment,
                    'canDelete' => Video_SecurityHelper::userCanDeleteComment($this->_user,$comment),
                    'deleteEndpoint' => $this->_buildUrl('comment','delete', array('xn_out' => 'json')),
                    'canApprove' => false,
                    'approveEndpoint' => null);
            }
            XG_CommentHelper::outputComments(array(
                    'commentData' => $commentData,
                    'numComments' => $this->numComments,
                    'pageSize' => $this->pageSize,
                    'attachedTo' => $this->video,
                    'currentUserCanSeeAddCommentSection' => $this->_user->isLoggedIn() ? Video_SecurityHelper::passed(Video_SecurityHelper::checkCurrentUserCanComment($this->_user, $this->video)) : true,
                    'commentsClosedText' => false,
                    'createCommentEndpoint' => $this->_buildUrl('comment','create', array('videoId' => $this->video->id, 'json'=>'yes')),
                    'showFollowLink' => false,
                    'feedUrl' => null,
                    'feedTitle' => null,
                    'feedFormat' => null,
                    'newestCommentsFirst' => false)); ?>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php
if ($this->video->my->conversionStatus == 'complete') { ?>
    <input type="hidden" id="video-url" value="<?php echo xnhtmlentities(Video_VideoHelper::videoAttachmentUrl($this->video)) ?>" />
    <input type="hidden" id="frame-url" value="<?php echo xnhtmlentities(Video_VideoHelper::previewFrameUrl($this->video)) ?>" />
    <input type="hidden" id="watermark-url" value="<?php echo ($this->_widget->privateConfig['playerLogoType'] == 'watermark_image')?xnhtmlentities($this->_widget->privateConfig['playerLogoUrl']):'' ?>" />
<?php
} ?>
<?php
// [Zuardi-07-Feb-2007] loading xg.video.video.show separately at the end because of IE [BAZ-1438]
// is xg.index.actionicons needed here? [ywh 2008-06-25]
XG_App::ningLoaderRequire('xg.video.index._shared', 'xg.index.actionicons', 'xg.video.video.show', 'xg.video.video.ShowEmbedToggle'); ?>
<?php if (mb_strlen($this->video->my->lat)) { XG_MapHelper::outputScriptTag(); }  ?>
<?php
$extraHtml = '<script src="' . xg_cdn('/xn_resources/widgets/video/js/video/player.js') . '" type="text/javascript"></script>';
if ((!$disableOthers)&&($isNing)) {
    $extraHtml .= '<script src="http://widgets.clearspring.com/launchpad/include.js" type="text/javascript"></script>';
}
xg_footer($extraHtml);?>
