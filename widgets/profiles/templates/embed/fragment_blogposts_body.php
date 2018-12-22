<?php
/** This partial template renders the body of the blog posts embed. It's in a separate partial template
 * so that it can easily be rendered in response to an embed reconfiguration
 *
 * @param $posts array The posts to display
 * @param $feedUrl string Subscription URL
 * @param $embed XG_Embed The XG_Embed object that is displaying these posts
 * @param $archiveUrl string optional Archive URL
 * @param $showCreateLink boolean Show the link to create a new blog post? Defaults to false
 * @param $showPromotionLinks boolean Show promotion links? Defaults to false
 * @param $hidePostDescription boolean Displays only the post titles? Defaults to false
 * @param $feedAutoDiscoveryTitle string  The title for the feed-autodiscovery element, or null to
 *         skip adding the feed-autodiscovery element to the head of the document
 * @param integer $maxEmbedWidth  The maximum width for <embed>s, in pixels
 */
$showCreateLink = isset($showCreateLink) ? $showCreateLink : false;
$showPromotionLinks = isset($showPromotionLinks) ? $showPromotionLinks : false; ?>
<?php XG_App::addToCssSection('<link rel="stylesheet" type="text/css" media="screen,projection" href="'.xnhtmlentities(XG_Version::addXnVersionParameter($this->_widget->buildResourceUrl('css/module.css'))).'" />');?>
<div class="xg_module_body body_detail">
<?php
    foreach ($posts as $post) {
        $postUrl = xnhtmlentities($this->_buildUrl('blog','show',array('id' => $post->id))); ?>
            <div class="blogpost vcard">
                <div class="ib">
                    <%= xg_avatar($post->contributorName, 32) %>
                </div>
                <div class="tb">
                    <h3><a href="<%= $postUrl  %>"><%= xnhtmlentities(BlogPost::getTextTitle($post)) %></a></h3>
                    <?php
                    if (! $hidePostDescription) {
                        $summary = BlogPost::summarize($post); ?>
                        <p class="postbody">
                            <%= xg_resize_embeds($summary, $maxEmbedWidth) %>
                            <?php if (($summary != $post->description) || (mb_strlen($post->title) == 0) || mb_strlen($post->description) > 500) { ?> <a href="<%= $postUrl %>"><%= xg_html('CONTINUE') %></a><?php } ?>
                        </p>
                    <?php
                    } ?>
                    <p class="small xg_lightfont">
                        <?php
                        $publishTimestamp = strtotime($post->my->publishTime);
                        $date = xg_date(xg_text('F_JS_Y'),$publishTimestamp);
                        $time = xg_date(xg_text('G_IA'),$publishTimestamp);
                        $commentCounts = Comment::getCounts($post);
                        $noDash = $commentCounts['approvedCommentCount'] == 0 ? '_NO_DASH' : '';
                        if ($this->embed->getType() == 'homepage') {
                            $href = 'href="' . xnhtmlentities(User::quickProfileUrl($post->contributorName)) . '"'; ?>
                            <%= mb_strlen($this->_user->screenName) && $this->_user->screenName == $post->contributorName ? xg_html('POSTED_BY_ME_LINK_ON_X_AT_X' . $noDash, $href, $date, $time) : xg_html('POSTED_BY_X_ON_X_AT_X' . $noDash, '<a ' . $href . '>' . xnhtmlentities(xg_username($post->contributor)) . '</a>', $date, $time) %>
                        <?php
                        } else { ?>
                            <%= xg_html('POSTED_ON_X_AT_X', $date, $time) %>
                        <?php
                        } ?>
                            <?php
                            if ($commentCounts['approvedCommentCount'] && $commentCounts['approvedCommentCount'] > 0) { ?>
                                <a href="<%= $postUrl %>#comments" class="xg_lightfont"><%= xg_html('N_COMMENTS', $commentCounts['approvedCommentCount']) %></a>
                            <?php
                            } ?>
                        <?php
                        if (($this->_user->screenName == $post->contributorName) &&
                            ($this->embed->getType() == 'profiles') && $this->embed->isOwnedByCurrentUser()) { ?>
                            <a href="<%= xnhtmlentities($this->_buildUrl('blog','edit',array('id' => $post->id))) %>" class="desc edit"><%= xg_html('EDIT_POST') %></a> &#160;
                        <?php
                        }
                        if ($showPromotionLinks) {
                            $actionAfter = ($this->embed->getType() == 'homepage') ? 'dojo.html.hide(this.link); window.location.reload()' : '';
                            W_Cache::getWidget('main')->dispatch('promotion','link',array($post, 'post', $actionAfter));
                        } ?>
                    </p>
                </div>
            </div>
    <?php
    } ?>
</div>
<?php
$showFeedLink = ! XG_App::appIsPrivate();
if ($showFeedLink && $feedAutoDiscoveryTitle) { xg_autodiscovery_link($feedUrl, $feedAutoDiscoveryTitle, 'atom'); }
if ($showFeedLink || $archiveUrl) { ?>
    <div class="xg_module_foot">
        <ul>
            <?php if ($showCreateLink) { ?><li class="left"><a href="<%= xnhtmlentities($this->_buildUrl('blog','new')) %>" class="desc add"><%= xg_html('ADD_A_BLOG_POST2') %></a></li> <?php } ?>
            <?php if ($archiveUrl) { ?><li class="right"><a href="<%= xnhtmlentities($archiveUrl) %>"><%= xg_html('VIEW_ALL') %></a></li><?php } ?>
        </ul>
    </div>
<?php
} ?>
