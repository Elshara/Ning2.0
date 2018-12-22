<?php
// TODO: This logic appears in a few places. Extract it to a helper function. [Jon Aquino 2008-02-20]
if ($this->coverPhoto[0] && $this->coverPhoto[0]->my->approved != 'N') {
    Photo_HtmlHelper::fitImageIntoThumb($this->coverPhoto[0], 165, 165, $coverPhotoUrl, $coverPhotoWidth, $coverPhotoHeight, true);
} else {
    $coverPhotoUrl = xg_cdn(W_Cache::getWidget('photo')->buildResourceUrl('gfx/albums/default_cover_120x120.gif'));
}
XG_App::addToSection('<link rel="image_src" href="' . $coverPhotoUrl . '" type="image/jpeg" />');
XG_App::addToSection('<meta name="title" content="' . xnhtmlentities($this->album->title) .'" />');
xg_header(W_Cache::current('W_Widget')->dir, $this->album->title, NULL, array(
    'metaDescription' => $this->album->description,
    'showFacebookMeta' => $this->showFacebookMeta
));
XG_App::ningLoaderRequire('xg.photo.album.show');
if (! XG_App::appIsPrivate()) { Photo_HtmlHelper::outputFeedAutoDiscoveryLink($this->_buildUrl('album','show', '?rss=yes&xn_auth=no&id=' . $this->album->id), $this->album->title); }
$disableOthers = file_exists(NF_APP_BASE . "/xn_private/disable_others") || file_exists(NF_APP_BASE . "/lib/disable_others");
if (!$disableOthers) {
    W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_ClearspringHelper.php');
}
?><div id="xg_body">
    <div class="xg_column xg_span-16">
		<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget,'album')) ?>
        <?php
        XG_CommentHelper::outputStoppedFollowingNotification(xg_text('NO_LONGER_FOLLOWING_ALBUM'));
        if ($_GET['shareInvitesSent']) { ?>
            <div class="xg_module">
                <div class="xg_module_body success">
                    <p class="last-child"><%= xg_html($_GET['shareInvitesSent'] > 1 ? 'YOUR_MESSAGES_SENT' : 'YOUR_MESSAGE_SENT') %></p>
                </div>
            </div>
        <?php
        } ?>
        <div class="xg_module xg_module_with_dialog">
			<?php
			$contributor = XG_Cache::profiles($this->album->contributorName);
			$date = xg_date(xg_text('F_J_Y'), $this->album->createdDate);
			$time = xg_date(xg_text('G_IA'), $this->album->createdDate);
			echo xg_headline($this->album->title, array(
					'avatarUser' => $contributor,
					'byline1Html' => xg_html('ADDED_BY_USER_ON_DATE_AT_TIME', xg_userlink($contributor), xnhtmlentities($date), xnhtmlentities($time)),
					'byline2Html' => xg_message_and_friend_links($this->album->contributorName, $this->_buildUrl('album', 'listForOwner', array('screenName' => $contributor->screenName)), xg_text('VIEW_ALBUMS')),
				));
			?>
            <div class="xg_module_body nopad body_albumdetail_main">
                <div class="xg_column xg_span-4">
                    <div class="albuminfo pad5">
                        <div style="background-image: url(<%= $coverPhotoUrl %>);" class="albumcover">
                            <%= xnhtmlentities($this->album->title) %>
                        </div>
                        <?php
                        if (mb_strlen($this->album->description)) { ?>
                            <p><%= xg_nl2br(xg_resize_embeds($this->album->description), 171) %></p>
                        <?php
                        }
                        $links = array();
                        if (count($this->photos) > 0) {
                            list($slideshowUrl, $feed_url) = Photo_SlideshowHelper::urls(array('albumId' => $this->album->id, 'sort' => $_GET['sort']));
                            $links[] = '<a class="play desc" href="' . xnhtmlentities($slideshowUrl) . '">' . xg_html('VIEW_SLIDESHOW') . '</a>';
                            ob_start();
                            $this->_widget->dispatch('photo', 'embeddable', array(array_merge($_GET, array(
                                'photoSet' => 'album_'.$this->album->id,
                                'includeFooterLink' => true,
                                'width' => 275,
                                'height' => 234
                                 ))));
                            $this->embedCode = preg_replace('/\s+/u', ' ', trim(ob_get_contents()));
                            ob_end_clean();
                            XG_App::includeFileOnce('/lib/XG_ShareHelper.php');
                            $app = XN_Application::load();
                            $this->appName = $app->name;
                            $this->appUrl = 'http://' . $_SERVER['HTTP_HOST'];
                            $myspaceUrl = XG_ShareHelper::postToMyspaceUrl($this->album->title, $this->embedCode, $this->appUrl, 5);
                            $facebookUrl = XG_ShareHelper::postToFacebookUrl($this->_buildUrl('album', 'show', '?id=' . $this->album->id), $this->album->title);
                            if (!$disableOthers) {
                                $config = Index_ClearspringHelper::extractConfigJson($this->embedCode, '&href='.$this->appUrl. '&networkName='. $this->appName);
                            }
                            $links[] = '<a class="desc embed" href="#"
                                   dojoType="ShowEmbedToggle"
                                   _widgetType="PhotoAlbum"
                                   _toOpenText="'. xg_html('GET_EMBED_CODE') .'"
                                   _toCloseText="'. xg_html('HIDE_EMBED_CODE') .'"
                                   _directURL="'. xnhtmlentities($this->_buildUrl('album', 'show', '?id=' . $this->album->id)) .'"
                                   _embedCode="'. xnhtmlentities($this->embedCode) .'"
                                   _myspacePostUrl="'. $myspaceUrl .'"
                                   _facebookPostUrl="'. $facebookUrl .'"
                                   _othersCustomCSS="'. (!$disableOthers ? Index_ClearspringHelper::getClearspringCssUrl() : '') .'"
                                   _disableOthers="'. $disableOthers . '"
                                   _widgetId="483eff8ba10f7fdc"
                                   _config="'. xnhtmlentities($config) .'"
                                   _isNing="1"
                                   >'. xg_html('GET_EMBED_CODE') .'</a>';
                        }
                        if (Photo_SecurityHelper::passed(Photo_SecurityHelper::checkCurrentUserCanEditAlbum($this->_user, $this->album))) {
                            $links[] = '<a class="desc edit" href="' . xnhtmlentities($this->_buildUrl('album', 'edit') . '?id=' . $this->album->id) . '">' . xg_html('EDIT_ALBUM') . '</a>';
                        }
                        if (Photo_SecurityHelper::passed(Photo_SecurityHelper::checkCurrentUserCanDeleteAlbum($this->_user, $this->album))) {
                            XG_App::ningLoaderRequire('xg.shared.PostLink');
                            $links[] = '<a class="desc delete" href="#" dojoType="PostLink" _confirmTitle="' . xg_html('DELETE_ALBUM') . '" _confirmOkButtonText="' . xg_html('DELETE') . '" _confirmQuestion="' . xg_html('DELETE_THIS_ALBUM') . '" _url="' . xnhtmlentities($this->_buildUrl('album', 'delete', array('id' => $this->album->id, 'target' => $this->_buildUrl('album', 'listForOwner', array('screenName' => $this->album->contributorName))))) . '">' . xg_html('DELETE_ALBUM') . '</a>';
                        }
                        if ($links) {
                            echo '<p class="relative">' . implode('<br />', $links) . '</p>';
                        }
                        $links = array();
                        if (Photo_PrivacyHelper::canCurrentUserSeeShareLinks($this->album)) {
                            $links[] = '<a class="desc share" href="' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('sharing', 'share', array('id' => urlencode($this->album->id)))) . '">' . xg_text('SHARE') . '</a>&nbsp;';
                        }
                        ob_start();
                        W_Cache::getWidget('main')->dispatch('promotion', 'link', array($this->album, 'album'));
                        $output = trim(ob_get_contents());
                        ob_end_clean();
                        if ($output) { $links[] = $output; }
                        if ($links) {
                            echo '<p class="clear">' . implode('&nbsp;', $links) . '</p>';
                        } ?>
                    </div>
                </div>
                <div class="xg_column xg_span-12 last-child">
                    <ul class="clist">
                        <?php
                        foreach ($this->photos as $photo) {
                            Photo_HtmlHelper::fitImageIntoThumb($photo, 124, 124, $imgUrl, $imgWidth, $imgHeight); ?>
                            <li><a href="<%= xnhtmlentities($this->_buildUrl('photo', 'show', array('id' => $photo->id, 'context' => 'album', 'albumId' => $this->album->id)  )) %>"><img width="<%= $imgWidth %>" height="<%= $imgHeight %>" src="<%= xnhtmlentities($imgUrl) %>" alt="" /></a></li>
                        <?php
                        } ?>
                    </ul>
                    <?php XG_PaginationHelper::outputPagination($this->numPhotos, $this->pageSize, 'easyclear'); ?>
                </div>
            </div>

        </div>
        <?php
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        XG_CommentHelper::outputStandardComments(array(
                'attachedTo' => $this->album,
                'commentController' => 'albumcomment',
                'pageParamName' => 'commentPage')); ?>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php
XG_App::ningLoaderRequire('xg.video.index._shared', 'xg.video.video.ShowEmbedToggle');
if (! isset($_GET['page'])) {
    /* TODO: Move this inline JavaScript into show.js [Jon Aquino 2008-02-20] */ ?>
    <script type="text/javascript">xg.addOnRequire(function() { incrementViewCount('<?php echo $this->album->id ?>'); });</script>
<?php
}
if (!$disableOthers) {
    $extraHtml = '<script src="http://widgets.clearspring.com/launchpad/include.js" type="text/javascript"></script>';
}
xg_footer($extraHtml);?>
