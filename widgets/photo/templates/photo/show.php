<?php xg_header(W_Cache::current('W_Widget')->dir, $this->photo->title, null, array('metaKeywords' => $this->photo->my->topTags, 'metaDescription' => $this->photo->description)); ?>
<?php if (! is_null($this->commentFeedUrl)) {
    xg_autodiscovery_link($this->commentFeedUrl, xg_text('COMMENTS_TITLE', $this->photo->title), 'atom');
} ?>
<?php XG_App::ningLoaderRequire('xg.photo.photo.show') ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
		<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget)) ?>
        <?php
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
            if (Photo_PhotoHelper::isAwaitingApproval($this->photo) || $_GET['test_awaiting_approval']) { ?>
                <div class="xg_module_body notification">
                    <p><%= xg_html('PHOTO_IS_WAITING_FOR_APPROVAL', xnhtmlentities(XN_Application::load()->name)) %></p>
                </div>
            <?php
            }
            $contributor = XG_Cache::profiles($this->photo->contributorName);
			$date = xg_date(xg_text('F_J_Y'), $this->photo->createdDate);
			$time = xg_date(xg_text('G_IA'), $this->photo->createdDate);
			$right = array();
			if ($this->previousPhoto) {
				$right[] = '<a title="' . qh($this->previousPhoto->title) .'" href="' . qh($this->_buildUrl('photo', 'show', array('id' => $this->previousPhoto->id, 'context' => $this->context, 'albumId' => $this->albumId))) . '">' . xg_html('PREVIOUS') . '</a>';
			} else {
			    $right[] = '<a class="disabled nolink">' . xg_html('PREVIOUS') . '</a>';
			}
            // if ($this->previousPhoto && $this->nextPhoto) {
                $right[] = '<a class="disabled nolink">|</a>';
            // }
			if ($this->nextPhoto) {
				$right[] = '<a title="' . qh($this->nextPhoto->title) . '" href="' . qh($this->_buildUrl('photo', 'show', array('id' => $this->nextPhoto->id, 'context' => $this->context, 'albumId' => $this->albumId))) . '">' . xg_html('NEXT') . '</a>';
			} else {
			    $right[] = '<a class="disabled nolink">' . xg_html('NEXT') . '</a>';
			}
			echo xg_headline($this->photo->title, array(
				'avatarUser' => $contributor,
				'byline1Html' => xg_html('ADDED_BY_USER_ON_DATE_AT_TIME', xg_userlink($contributor), xnhtmlentities($date), xnhtmlentities($time)),
				'byline2Html' => xg_message_and_friend_links($this->photo->contributorName, $this->_buildUrl('photo', 'listForContributor', array('screenName' => $contributor->screenName)), xg_text('VIEW_PHOTOS')),
				'rightNavHtml' => $right ? join(' ', $right) : '',
			));
		   	?>
            <div class="xg_module_body">
                <div class="imgarea" >
                    <p class="small last-child nobr right">
                    </p>
                    <div class="mainimg easyclear">
                        <div class="photo" style="<?php if ($this->scaledHeight < 200) { ?>height:200px; line-height:200px;<?php } ?>">
                            <a target="_blank" href="<%= xnhtmlentities($this->originalUrl) %>"><img width="<%= $this->scaledWidth %>" height="<%= $this->scaledHeight %>" alt="" src="<%= xnhtmlentities($this->scaledUrl) %>" /></a>
                        </div>
                        <div class="description"><%= xg_nl2br(xg_resize_embeds($this->photo->description, 737)) %></div>
                    </div>
                    <div class="edit_options">
                        <?php
                        if ($this->_user->isLoggedIn()) {
                            $rating = Photo_UserHelper::getRating($this->user, $this->photo->id);
                            $setRatingUrl = $this->_buildUrl('rating', 'update', '?photoId=' . $this->photo->id); ?>
                            <small class="left"><%= xg_html('RATE_COLON') %>&nbsp;</small> <%= xg_rating_widget($rating, $setRatingUrl, 'xj_photo_rating') %> <br/>
                        <?php
                        } ?>
                        <a target="_blank" class="desc view" href="<%= xnhtmlentities($this->originalUrl) %>"><%= xg_html('VIEW_FULL_SIZE') %></a><br/>
                        <?php
                        // TODO: Put this check in Photo_SecurityHelper::currentUserCanEditPhoto($photo) [Jon Aquino 2008-02-08]
                        if (XG_SecurityHelper::userIsContributor($this->_user, $this->photo)) {
                        ?>
                            <a class="desc rotate" href="#" dojoType="PostLink" _url="<%= xnhtmlentities($this->_buildUrl('photo', 'rotate', array('id' => $this->photo->id, 'target' => XG_HttpHelper::currentUrl(), 'save' => 1))) %>"><%= xg_html('ROTATE_PHOTO') %></a><br/>
                            <a class="desc edit" href="<%= xnhtmlentities($this->_buildUrl('photo', 'edit', '?id=' . $this->photo->id)) %>"><%= xg_html('EDIT_PHOTO') %></a><br/>
                        <?php
                        }
                        if (XG_SecurityHelper::userIsAdminOrContributor($this->_user, $this->photo)) {
                            XG_App::ningLoaderRequire('xg.index.bulk');
                            // TODO: This refreshes the page, so use a PostLink instead of Ajax [Jon Aquino 2008-02-08]
                            XG_App::ningLoaderRequire('xg.shared.PostLink', 'xg.shared.TagLink');
                            $addOrEdit = mb_strlen($this->currentUserTagString) ? 'edit' : 'add'; ?>
                            <span class="relative">
                            <span dojoType="TagLink"
                                _actionUrl="<%= xnhtmlentities($this->_buildUrl('photo', 'tag', array('id' => $this->photo->id, 'xn_out' => json))); %>"
                                _popOver="true"
                                _tags="<%= xnhtmlentities($this->currentUserTagString); %>"">
                                <a class="desc <%= $addOrEdit %>" href="#"><%= $addOrEdit == 'edit' ? xg_text('EDIT_YOUR_TAGS') : xg_text('ADD_TAGS') %></a>
                            </span>
                            </span><br/>
                            <a class="desc delete" href="#"
                                    dojoType="BulkActionLink"
                                    title ="<%= xg_html('DELETE_THIS_PHOTO_Q') %>"
                                    _confirmMessage ="<%= xg_html('ARE_YOU_SURE_DELETE_THIS_PHOTO', xnhtmlentities(Photo_FullNameHelper::fullName($this->user->title))) %>"
                                    _url = "<%= xnhtmlentities($this->_buildUrl('bulk', 'remove', array('limit' => 20, 'id' => $this->photo->id, 'xn_out' => 'json'))) %>"
                                    _successUrl = "<%= xnhtmlentities($this->context ? Photo_Context::get($this->context)->getListPageUrl($this->photo) : $this->_buildUrl('photo', 'index')) %>"
                                    _progressTitle = "<%= xg_html('DELETING') %>"
                                    ><%= xg_html('DELETE_PHOTO') %></a><br/>
                        <?php
                        } ?>
                    </div>
                    <p class="small">
                        <%= xg_html('RATING') %>
						<span id="xj_photo_rating"><%= xg_rating_image($this->photo->my->ratingAverage) %></span><br/>
                        <?php
                        if ($this->tags) {
                            $tagUrl = W_Cache::getWidget('photo')->buildUrl('photo', 'listTagged'); ?>
                            <span id="tagsList"><%= xg_html('TAGS') %> <%= xg_tag_links($this->tags, $tagUrl, 5, true) %></span><br/>
                        <?php
                        }
                        if ($this->albums) {
                            $links = array();
                            foreach ($this->albums as $album) {
                                $links[] = '<a href="' . xnhtmlentities($this->_buildUrl('album', 'show') . '?id=' . $album->id) . '">' . xnhtmlentities($album->title) . '</a>';
                            }
                            echo xg_html('ALBUMS') . ' ' . xg_links_with_more($links); ?>
                            <br/>
                        <?php
                        } ?>
                        <%= xg_html('VIEWS') %> <%= xnhtmlentities($this->photo->my->viewCount + 1) %><br/>
                        <?php
                        if ($this->photo->my->favoritedCount) { ?>
                            <%= xg_html('FAVORITE_OF_N_PEOPLE', $this->photo->my->favoritedCount) %><br/>
                        <?php
                        }
                        $locationLinks = xg_location_links($this->photo->my->lat, $this->photo->my->lng, $this->photo->my->locationInfo, $this->photo->my->location, $this->_buildUrl('photo', 'listForLocation', array('location' => $this->photo->my->location)), $mapDiv);
                        if ($locationLinks) { ?>
                            <%= xg_html('LOCATION_COLON') %> <%= $locationLinks %><br/>
                        <?php
                        } ?>
                    </p>
                    <%= $mapDiv %>
                </div>
                <?php
                if ($this->_user->isLoggedIn() || Photo_PrivacyHelper::canCurrentUserSeeShareLinks($this->photo)) { ?>
                    <p class="clear">
                        <?php
                        if (Photo_PrivacyHelper::canCurrentUserSeeShareLinks($this->photo)) { ?>
                            <a class="desc share" href="<%= xnhtmlentities(W_Cache::getWidget('main')->buildUrl('sharing', 'share', array('id' => urlencode($this->photo->id)))) %>"><%= xg_text('SHARE') %></a>&nbsp;
                        <?php
                        }
                        if ($this->_user->isLoggedIn()) {
                            XG_App::ningLoaderRequire('xg.shared.FavoriteLink'); ?>
                            <a class="desc <%= Photo_UserHelper::hasFavorite($this->user, $this->photo->id) ? 'favorite-remove' : 'favorite-add' %>" dojotype="FavoriteLink" href="#"
                                    _addurl="<%= $this->_buildUrl('user', 'favorize', '?photoId=' . $this->photo->id); %>"
                                    _removeUrl="<%= $this->_buildUrl('user', 'defavorize', '?photoId=' . $this->photo->id); %>"
                                    _hasFavorite="<%= Photo_UserHelper::hasFavorite($this->user, $this->photo->id) %>">
                                <%= Photo_UserHelper::hasFavorite($this->user, $this->photo->id) ? xg_html('REMOVE_FROM_FAVORITES') : xg_html('ADD_TO_FAVORITES') %>
                            </a>&nbsp;
                            <?php W_Cache::getWidget('main')->dispatch('promotion', 'link', array($this->photo, 'photo')); ?>
                        <?php
                        } ?>
                    </p>
                <?php
                } ?>
            </div>
        </div>
        <?php
        // TODO: Put this check in Photo_SecurityHelper::currentUserCanSeeAddCommentSection($photo) [Jon Aquino 2008-02-09]
        $currentUserCanSeeAddCommentSection = $this->_user->isLoggedIn()
                ? Photo_SecurityHelper::passed(Photo_SecurityHelper::checkCurrentUserCanComment($this->_user, $this->photo))
                : Photo_UserHelper::get(Photo_UserHelper::load($this->photo->contributorName), 'addCommentPermission') != 'me';
        $commentData = array();
        foreach ($this->comments as $comment) {
            $commentData[] = array(
                'comment' => $comment,
                // TODO: Put this check in Photo_SecurityHelper::currentUserCanDeleteComment($comment) [Jon Aquino 2008-02-08]
                'canDelete' => $this->_user->screenName == $comment->my->attachedToAuthor || XG_SecurityHelper::userIsAdminOrContributor($this->_user, $comment),
                'deleteEndpoint' => $this->_buildUrl('comment','delete', array('xn_out' => 'json')),
                'canApprove' => false,
                'approveEndpoint' => null);
        }
        XG_CommentHelper::outputComments(array(
                'commentData' => $commentData,
                'numComments' => $this->numComments,
                'pageSize' => $this->pageSize,
                'attachedTo' => $this->photo,
                'currentUserCanSeeAddCommentSection' => $currentUserCanSeeAddCommentSection,
                'commentsClosedText' => false,
                'createCommentEndpoint' => $this->_buildUrl('comment','create', array('photoId' => $this->photo->id)),
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
<script type="text/javascript">xg.addOnRequire(function() { xg.photo.photo.show.incrementViewCount('<?php echo $this->photo->id ?>'); });</script>
<?php if (mb_strlen($this->photo->my->lat)) { XG_MapHelper::outputScriptTag(); }  ?>
<?php xg_footer(); ?>
