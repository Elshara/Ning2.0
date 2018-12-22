<?php
/**
* @param $item W_Content | XN_Content the activity log item
* @param $isProfile boolean whether the embed is being displayed on a profile page
* @param $profileOwner string the screenName of the owner of the embed object
* @param $lastChild boolean whether or not to append the last-child class to the div
*
**/

// TODO: Redesign this page to be cleaner, scalable,
// free of duplicate code, and I18N-friendly. [Jon Aquino 2008-02-23]
$titleMaxLength = 160;
try {
    //TODO: might be safer to do the ob_start/ob_end outside this file, i.e., in whatever calls this file. [Thomas David Baker 2008-10-09]
    ob_start();
    XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
    XG_App::includeFileOnce('/lib/XG_FullNameHelper.php');
    XG_App::includeFileOnce('/lib/XG_TemplateHelpers.php');
    W_Cache::getWidget('activity')->includeFileOnce('/lib/helpers/Activity_LogHelper.php');
    $members         = explode(',',$item->my->members);
    $username       = $members[0];
    $fullname       = XG_FullNameHelper::fullName($username);
    $contentIds     = explode(',',$item->my->contents);
    if (mb_strlen(trim($item->my->contents)) && is_null(XG_Cache::content($contentIds[0]))) {
        // if we have a multi item object, determine if only the first item has been deleted and show the rest: BAZ-9015
        if (count($contentIds) > 1 && ! is_null(XG_Cache::content($contentIds[1]))) {
            $contentIds = array_slice($contentIds, 1);
        } else {
            ob_end_clean();
            return;
        }
    }
    $screenNames    = explode(',',$item->my->members);
    $titles         = explode(',',$item->my->titles);
    $idList         = ($item->my->tempIdList) ? $item->my->tempIdList : $item->id;
    $currentpage    = urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    $avatarSize     = 32;
    $app            = XN_Application::load();
    $appName        = $app->name;
    $fmt            = $fmt ? $fmt : 'html';

    //fact, statement, or neither
    $isFact         = Activity_LogHelper::isFact($item);
    $isStatement    = Activity_LogHelper::isStatement($item);
    $isFeature      = Activity_LogHelper::isFeature($item);

    if ($fmt=='html') {
        $timeStamp = '<span class="time xg_lightfont">' . xg_elapsed_time($item->createdDate) . '</span>';
    }
    ?>
    <div class="xg_module_body activityitem <%= $item->my->subcategory %>_activity_item <%= $lastChild ? 'last-child' : '' %>" <%= '_owners="'.xnhtmlentities($item->my->members).'" _idList="'.xnhtmlentities($idList).'"' %>><?php
    if($isFact || $isStatement || $isFeature){ ?>
            <div class="<%= $isFact?'question':($isStatement ? 'statement' : 'featured') %>"></div>
        <?php
    }
    //log item starts with the avatar link in the following cases
    if (Activity_LogHelper::hasAvatar($item)){
        if(! $isProfile && !$isFeature) { ?>
                <%= xg_avatar(XG_Cache::profiles($username), $avatarSize, 'thumb') %> <?php
        }
    }
    if((!$isProfile)||($username != $profileOwner)){
        $who = '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($username)).'">'.xnhtmlentities($fullname).'</a>';
    } else {
        $who = xnhtmlentities($fullname);
    }

    //opensocial app added, or opensocial activity posted to activity stream.
    if ($item->my->category == XG_ActivityHelper::CATEGORY_OPENSOCIAL) {
        if (! XG_App::openSocialEnabled()) { ob_end_clean(); return; } // Last-ditch check in case we have employed the global kill switch which does not clear out existing log items.
        $appData = XG_Cache::content($contentIds[0]);
        $aboutPageHref = 'href="' . xnhtmlentities(W_Cache::getWidget('opensocial')->buildUrl('application', 'about', array('appUrl' => $appData->my->appUrl))) . '"';
        if ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_ADD_APP || $item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_APP_REVIEW) {
            $keyword = ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_ADD_APP ? 'ADDED' : 'REVIEWED');
            $appTitle = xnhtmlentities($item->description);
            ?>
            <span class="message">
            <?php
            if (XG_App::onMyProfilePage() && $isProfile) {
                if ($appTitle) {
                    echo xg_html('YOU_' . $keyword . '_THE_X_APPLICATION', $aboutPageHref, $appTitle);
                } else {
                    echo xg_html('YOU_' . $keyword . '_AN_APPLICATION', $aboutPageHref);
                }
            } else {
                $profileHref = 'href="http://' . xnhtmlentities($_SERVER['HTTP_HOST'] . User::quickProfileUrl($members[0])) . '"';
                $name = xnhtmlentities(XG_FullNameHelper::fullName($members[0]));
                if ($appTitle) {
                    echo xg_html('X_' . $keyword . '_THE_Y_APPLICATION', $profileHref, $name, $aboutPageHref, $appTitle);
                } else {
                    echo xg_html('X_' . $keyword . '_AN_APPLICATION', $profileHref, $name, $aboutPageHref);
                }
            } ?>
            </span>
            <?php
        } else if ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_APP_MSG) {
            W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
            $gadgetPrefs = OpenSocial_GadgetHelper::readGadgetUrl($appData->my->appUrl);
            $appTitle = $gadgetPrefs['title'];
            /* In case of opensocial - 'title' is the description of the activity */
            ?>
            <span class="message">
                <%= xnhtmlentities($item->title) %> <%= ($appTitle ? xg_html('VIA_APPNAME2', $aboutPageHref, $appTitle) : "") %>
            </span>
            <?php
        }
        ?>
        <%= $timeStamp %>
        <?php
    }

    // friend request accepted
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FRIEND)&&($item->my->category == XG_ActivityHelper::CATEGORY_CONNECTION)){ ?>
        <div class="friends"></div>
        <span class="message"><%= Activity_LogHelper::instance()->friendshipMessageHtml($members, $item->my->xg_profiles_friendCount, XG_App::onMyProfilePage(), XN_Profile::current()->screenName); %></span><%= $timeStamp %>
        <div class="thumbs">
            <?php
            foreach (XG_Cache::profiles($members) as $profile) {
                echo xg_avatar($profile, $avatarSize);
            } ?>
        </div>
    <?php
    }

    //new group created
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_GROUP)&&($item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT)){
        XG_App::includeFileOnce('/lib/XG_GroupHelper.php');
        $group = XG_Cache::content($contentIds[0]);
        $contentIds = array_unique($contentIds); ?>
        <span class="message"><?php
        if(count($contentIds) == 1){
            $item->title    = xg_text('X_CREATED_A_GROUP_Y', $fullname, xg_excerpt($group->title,$titleMaxLength));
            $item->my->link = XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $contentIds[0]));
            $groupLink = '<a href="'.xnhtmlentities($item->my->link).'">'.xnhtmlentities(xg_excerpt($group->title,$titleMaxLength)).'</a>'; ?>
            <%= ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('YOU_CREATED_A_GROUP_Y', $groupLink):xg_html('X_CREATED_A_GROUP_Y',$who, $groupLink) %></span><?php
        } else {
            $item->title    = xg_text('X_CREATED_Y_NEW_GROUPS', $fullname, xg_excerpt($group->title,$titleMaxLength));
            $item->my->link = W_Cache::getWidget('groups')->buildUrl('group', 'list'); ?>
            <%= xg_html('X_CREATED_Y_NEW_GROUPS', $who, count($contentIds)) %>. <a href="<%= $item->my->link %>"><%= xg_html('VIEW_GROUPS')%></a></span><?php
        }
        echo $timeStamp;
    }

    //new blog post created
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_BLOG)&&($item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT)){
        $blogPost = XG_Cache::content($contentIds[0]); ?>
        <span class="message"><?php
        if(count($contentIds) == 1){
            $item->title    = xg_text('X_ADDED_THE_BLOG_POST_Y', $fullname, BlogPost::getTextTitle($blogPost, $titleMaxLength));
            $item->my->link = 'http://' . $_SERVER['HTTP_HOST'] .'/xn/detail/' . $contentIds[0];
            $postLink = '<a href="'.xnhtmlentities($item->my->link).'">'.xnhtmlentities(BlogPost::getTextTitle($blogPost, $titleMaxLength)).'</a>'; ?>
            <%= ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('YOU_ADDED_THE_BLOG_POST_Y', $postLink):xg_html('X_ADDED_THE_BLOG_POST_Y', $who, $postLink) %></span><?php
        } else {
            $item->title    = xg_text('X_ADDED_Y_BLOG_POSTS', $fullname,  count($contentIds));
            $item->my->link = W_Cache::getWidget('profiles')->buildUrl('blog', 'list', array(
                // Fix for BAZ-7916: when the future post is published,
                // contributorName is the person who triggered publishing and not the actual post author.
                // It also fixes the case when the publishing was triggered by the anonymous user.
                'user' => count($members) == 1 ? $members[0] : $item->contributorName)
            ); ?>
            <%= ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('YOU_ADDED_Y_BLOG_POSTS', count($contentIds)):xg_html('X_ADDED_Y_BLOG_POSTS', $who, count($contentIds)) %>. <a href="<%= xnhtmlentities($item->my->link) %>"><%=
            ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('VIEW_YOUR_BLOG_POSTS'):xg_html('VIEW_XS_BLOG_POSTS', xnhtmlentities($fullname)) %></a></span><?php
        }
        echo $timeStamp;
    }

    //new topic created
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_TOPIC)&&($item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT)){
        $topic = XG_Cache::content($contentIds[0]); ?>
        <span class="message"><?php
        if(count($contentIds) == 1){
            $item->title    = xg_text('X_STARTED_A_DISCUSSION_Y', $fullname, xg_excerpt($topic->title,$titleMaxLength));
            $item->my->link = 'http://' . $_SERVER['HTTP_HOST'] .'/xn/detail/' . $contentIds[0];
            $postLink = '<a href="'.xnhtmlentities($item->my->link).'">'.xnhtmlentities(xg_excerpt($topic->title,$titleMaxLength)).'</a>'; ?>
            <%= ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('YOU_STARTED_A_DISCUSSION_Y', $postLink):xg_html('X_STARTED_A_DISCUSSION_Y', $who, $postLink) %></span><?php
        } else {
            $item->title    = xg_text('X_STARTED_Y_FORUM_POSTS', $fullname,  count($contentIds));
            $item->my->link = W_Cache::getWidget('forum')->buildUrl('topic', 'listForContributor', array('user' => $item->contributorName)); ?>
            <%= ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('YOU_STARTED_Y_FORUM_POSTS', count($contentIds)):xg_html('X_STARTED_Y_FORUM_POSTS', $who, count($contentIds)) %>. <a href="<%= xnhtmlentities($item->my->link) %>"><%=
            ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('VIEW_YOUR_DISCUSSIONS'):xg_html('VIEW_XS_DISCUSSIONS', xnhtmlentities($fullname)) %></a></span><?php
        }
        echo $timeStamp;
    }

    //new topic created in a group
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_GROUP_TOPIC)&&($item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT)){
        XG_App::includeFileOnce('/lib/XG_GroupHelper.php');
        $topic = XG_Cache::content($contentIds[0]);
        $item->my->link = 'http://' . $_SERVER['HTTP_HOST'] .'/xn/detail/' . $contentIds[0];
        $titles = explode(',', $item->my->titles); ?>
        <span class="message"><?php
        // Why are we urlencoding the titles? [Jon Aquino 2008-05-14]
        $groupTitleLink = '<a href="' . XG_GroupHelper::buildUrl('groups','group','show',array('id' => $topic->my->groupId)) . '">' . xnhtmlentities(urldecode($titles[0])) . '</a>';
        if(count($contentIds) == 2){
            $item->title    = xg_text('X_STARTED_A_DISCUSSION_Y_IN_GROUP', $fullname, xg_excerpt($topic->title,$titleMaxLength), urldecode($titles[0]));
            $postLink = '<a href="'.xnhtmlentities($item->my->link).'">'.xnhtmlentities(xg_excerpt($topic->title,$titleMaxLength)).'</a>'; ?>
            <%= ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('YOU_STARTED_A_DISCUSSION_Y_IN_GROUP', $postLink, $groupTitleLink ):xg_html('X_STARTED_A_DISCUSSION_Y_IN_GROUP', $who, $postLink, $groupTitleLink) %></span><?php
        } else {
            $contentIds = array_unique($contentIds);
            $item->title    = xg_text('X_STARTED_Y_FORUM_POSTS_IN_GROUP', $fullname,  count($contentIds)-1, $groupTitleLink);?>
            <%= ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('YOU_STARTED_Y_FORUM_POSTS_IN_GROUP', count($contentIds)-1,$groupTitleLink ):xg_html('X_STARTED_Y_FORUM_POSTS_IN_GROUP', $who, count($contentIds)-1, $groupTitleLink) %></span><?php
        }
        echo $timeStamp;
    }

    //new reply to a topic
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_TOPIC)&&($item->my->category == XG_ActivityHelper::CATEGORY_NEW_COMMENT)){
        W_Cache::getWidget('forum')->includeFileOnce('/lib/helpers/Forum_CommentHelper.php');
        $comment = XG_Cache::content($contentIds[0]);
        $commentHref = 'href="' . xnhtmlentities(Forum_CommentHelper::url($comment)). '"';
        $item->title    = xg_text('X_COMMENTED_ON_POST_TITLE', $fullname, xg_excerpt(urldecode($titles[0]),$titleMaxLength));
        $item->my->link = 'http://' . $_SERVER['HTTP_HOST'] .'/xn/detail/' . $contentIds[0];
        $replyHref =  'href="' . xnhtmlentities($item->my->link) . '"';
        $replyTitle = xnhtmlentities(xg_excerpt(urldecode($titles[0]),$titleMaxLength));
         ?>
         <span class="message">
        <%= ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('YOU_LINK_COMMENTED_ON_POST_LINK_TITLE', $commentHref, $replyHref , $replyTitle):xg_html('X_LINK_COMMENTED_ON_POST_LINK_TITLE', $who, $commentHref, $replyHref , $replyTitle) %>
        </span>
        <%= $timeStamp %>
    <?php
    }

    //new comment on blog post
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_BLOG)&&($item->my->category == XG_ActivityHelper::CATEGORY_NEW_COMMENT)){
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
        $comment = XG_Cache::content($contentIds[0]);
        $blogPost = XG_Cache::content($contentIds[1]);
        $commentHref = 'href="' . xnhtmlentities(Profiles_CommentHelper::url($comment)). '"';
        $item->title    = xg_text('X_COMMENTED_ON_BLOG_POST_TITLE', $fullname, BlogPost::getTextTitle($blogPost,$titleMaxLength));
        $item->my->link = 'http://' . $_SERVER['HTTP_HOST'] .'/xn/detail/' . $contentIds[1];
        $postHref =  'href="' . xnhtmlentities($item->my->link) . '"';
        $postTitle = xnhtmlentities(BlogPost::getTextTitle($blogPost,$titleMaxLength));
         ?>
        <span class="message">
        <%= ((XG_App::onMyProfilePage())&&($isProfile)&&($this->_user->screenName == $username))?xg_html('YOU_LINK_COMMENTED_ON_BLOG_POST_LINK_TITLE', $commentHref, $postHref , $postTitle):xg_html('X_LINK_COMMENTED_ON_BLOG_POST_LINK_TITLE', $who, $commentHref, $postHref , $postTitle) %>
        </span>
        <%= $timeStamp %>
    <?php
    }

    //new comment on group
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_GROUP)&&($item->my->category == XG_ActivityHelper::CATEGORY_NEW_COMMENT)){
        W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_CommentHelper.php');
        $comment = XG_Cache::content($contentIds[0]);
        $group = XG_Cache::content($contentIds[1]);
        $item->my->link = XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $contentIds[1]));
        $postHref =  'href="' . xnhtmlentities($item->my->link) . '"';
        $postTitle = xnhtmlentities(xg_excerpt($group->title,$titleMaxLength));
         ?>
        <span>
        <%= ((XG_App::onMyProfilePage())&&($isProfile)&&($this->_user->screenName == $username))?xg_html('YOU_COMMENTED_ON_GROUP_LINK_TITLE', $postHref , $postTitle):xg_html('X_COMMENTED_ON_GROUP_LINK_TITLE', $who, $postHref , $postTitle) %>
        </span>
        <%= $timeStamp %>
    <?php
    }

    //new comment on photo
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_PHOTO)&&($item->my->category == XG_ActivityHelper::CATEGORY_NEW_COMMENT)){
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_CommentHelper.php');
        $comment = XG_Cache::content($contentIds[1]);
        $photo = XG_Cache::content($contentIds[0]);
        $item->my->link = W_Cache::getWidget('photo')->buildUrl('photo', 'show', array('id' => $photo->id));
        $href = 'href="' . $item->my->link . '"';
        if (!$photo->title) {
            $photo->title = xg_text('UNTITLED');
        }
        $userSet = $who;
        if (count($screenNames > 1)) {
            // remove dups so our count doesn't get weird
            $screenNames = array_unique($screenNames);
        }
        // TODO: Do not use xg_html('AND') - it is English-specific, and
        // not translatable to all languages. Instead use
        // xg_html('LIST', \$n, \$item1, \$item2, ...) [Jon Aquino 2008-08-16]
        if (count($screenNames) == 2) {
            $userSet .= ' ' . xg_text('AND') . ' ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[1])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[1])).'</a>';
        }
        if (count($screenNames) == 3) {
            $userSet .= ', ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[1])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[1])).'</a>' . ' ' . xg_text('AND') . ' ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[2])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[2])).'</a>';
        }
        if (count($screenNames) > 3) {
            $userSet .= ', ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[1])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[1])).'</a>' . ', ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[2])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[2])).'</a> ' . xg_text('AND_X_OTHER_PEOPLE',count($screenNames)-3) . ' ';
        }

         ?>
        <span class="message">
        <%= ((XG_App::onMyProfilePage())&&($isProfile)&&($this->_user->screenName == $username))?xg_html('YOU_COMMENTED_ON_PHOTO_TITLE',$href, xnhtmlentities($photo->title)):xg_html('X_LINK_COMMENTED_ON_PHOTO_TITLE', $userSet, $href, xnhtmlentities($photo->title)) %>
        </span>
        <%= $timeStamp %>
    <?php
    }

    //new comment on album
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_ALBUM)&&($item->my->category == XG_ActivityHelper::CATEGORY_NEW_COMMENT)){
        $comment = XG_Cache::content($contentIds[1]);
        $album = XG_Cache::content($contentIds[0]);
        $item->my->link = W_Cache::getWidget('photo')->buildUrl('album', 'show', array('id' => $album->id));
        $href = 'href="' . $item->my->link . '"';
        if (!$album->title) {
            $album->title = xg_text('UNTITLED');
        }
        $userSet = $who;
        if (count($screenNames > 1)) {
            // remove dups so our count doesn't get weird
            $screenNames = array_unique($screenNames);
        }
        // TODO: Do not use xg_html('AND') - it is English-specific, and
        // not translatable to all languages. Instead use
        // xg_html('LIST', \$n, \$item1, \$item2, ...) [Jon Aquino 2008-08-16]
        if (count($screenNames) == 2) {
            $userSet .= ' ' . xg_text('AND') . ' ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[1])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[1])).'</a>';
        }
        if (count($screenNames) == 3) {
            $userSet .= ', ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[1])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[1])).'</a>' . ' ' . xg_text('AND') . ' ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[2])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[2])).'</a>';
        }
        if (count($screenNames) > 3) {
            $userSet .= ', ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[1])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[1])).'</a>' . ', ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[2])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[2])).'</a> ' . xg_text('AND_X_OTHER_PEOPLE',count($screenNames)-3) . ' ';
        } ?>
        <span class="message">
        <%= ((XG_App::onMyProfilePage())&&($isProfile)&&($this->_user->screenName == $username))?xg_html('YOU_COMMENTED_ON_ALBUM_TITLE',$href, xnhtmlentities($album->title)):xg_html('X_LINK_COMMENTED_ON_ALBUM_TITLE', $userSet, $href, xnhtmlentities($album->title)) %>
        </span>
        <%= $timeStamp %>
    <?php
    }

    //new comment on event
    if ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_EVENT) {
        $event = XG_Cache::content($contentIds[0]);
        $item->my->link = W_Cache::getWidget('events')->buildUrl('event', 'show', array('id' => $event->id));
        $href = 'href="' . xg_xmlentities($item->my->link) . '"';

        switch ($item->my->category) {
            case XG_ActivityHelper::CATEGORY_NEW_COMMENT:
                $comment = XG_Cache::content($contentIds[1]);
                $userSet = $who;
                if (count($screenNames > 1)) {
                    // remove dups so our count doesn't get weird
                    $screenNames = array_unique($screenNames);
                }
                // TODO: Do not use xg_html('AND') - it is English-specific, and
                // not translatable to all languages. Instead use
                // xg_html('LIST', \$n, \$item1, \$item2, ...) [Jon Aquino 2008-08-16]
                if (count($screenNames) == 2) {
                    $userSet .= ' ' . xg_text('AND') . ' ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[1])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[1])).'</a>';
                }
                if (count($screenNames) == 3) {
                    $userSet .= ', ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[1])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[1])).'</a>' . ' ' . xg_text('AND') . ' ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[2])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[2])).'</a>';
                }
                if (count($screenNames) > 3) {
                    $userSet .= ', ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[1])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[1])).'</a>' . ', ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[2])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[2])).'</a> ' . xg_text('AND_X_OTHER_PEOPLE',count($screenNames)-3) . ' ';
                }
                if (XG_App::onMyProfilePage() && $isProfile && $this->_user->screenName == $username) {
                    echo '<span class="message">' . xg_html('YOU_COMMENTED_ON_EVENT_TITLE', $href, xnhtmlentities($event->title)) . '</span>';
                } else {
                    echo '<span class="message">' . xg_html('X_LINK_COMMENTED_ON_EVENT_TITLE', $userSet, $href, xnhtmlentities($event->title)) . '</span>';
                }
                break;
            case XG_ActivityHelper::CATEGORY_STATUS_CHANGE:
                $attend = (bool)($item->description == EventAttendee::ATTENDING);
                $might = (bool)($item->description == EventAttendee::MIGHT_ATTEND);
                if (XG_App::onMyProfilePage() && $isProfile) {
                    echo '<span class="message">' . xg_html($attend ? 'YOU_CHANGED_EVENT_STATUS_ATTEND' : 'YOU_CHANGED_EVENT_STATUS_MIGHT', $href, xnhtmlentities($event->title)) . '</span>';
                } else {
                    echo '<span class="message">' . xg_html($attend ? 'X_CHANGED_EVENT_STATUS_ATTEND' : 'X_CHANGED_EVENT_STATUS_MIGHT', $who, $href, xnhtmlentities($event->title)) . '</span>';
                }
                break;
            case XG_ActivityHelper::CATEGORY_NEW_CONTENT:
                if (XG_App::onMyProfilePage() && $isProfile) {
                    echo '<span class="message">' . xg_html('YOU_CREATED_EVENT', $href, xnhtmlentities($event->title)) . '</span>';
                } else {
                    echo '<span class="message">' . xg_html('X_CREATED_EVENT_TITLE', $who, $href, xnhtmlentities($event->title)) . '</span>';
                }
                break;
            case XG_ActivityHelper::CATEGORY_UPDATE:
                if (XG_App::onMyProfilePage() && $isProfile) {
                    echo '<span class="message">' . xg_html('YOU_UPDATED_EVENT', $href, xnhtmlentities($event->title)) . "</span>";
                } else {
                    echo '<span class="message">' . xg_html('X_UPDATED_EVENT_TITLE', $who, $href, xnhtmlentities($event->title)) . "</span>";
                }
                break;
        }
        echo $timeStamp;
    }

    //new comment on video
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_VIDEO)&&($item->my->category == XG_ActivityHelper::CATEGORY_NEW_COMMENT)){
        W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_CommentHelper.php');
        $comment = XG_Cache::content($contentIds[1]);
        $video = XG_Cache::content($contentIds[0]);
        $item->my->link = W_Cache::getWidget('video')->buildUrl('video', 'show', array('id' => $video->id));
        $href = 'href="' . $item->my->link . '"';
        if (!$video->title) {
            $video->title = xg_text('UNTITLED');
        }
        $userSet = $who;
        if (count($screenNames > 1)) {
            // remove dups so our count doesn't get weird
            $screenNames = array_unique($screenNames);
        }
        // TODO: Do not use xg_html('AND') - it is English-specific, and
        // not translatable to all languages. Instead use
        // xg_html('LIST', \$n, \$item1, \$item2, ...) [Jon Aquino 2008-08-16]
        if (count($screenNames) == 2) {
            $userSet .= ' ' . xg_text('AND') . ' ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[1])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[1])).'</a>';
        }
        if (count($screenNames) == 3) {
            $userSet .= ', ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[1])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[1])).'</a>' . ' ' . xg_text('AND') . ' ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[2])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[2])).'</a>';
        }
        if (count($screenNames) > 3) {
            $userSet .= ', ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[1])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[1])).'</a>' . ', ' . '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($screenNames[2])).'">'.xnhtmlentities(XG_FullNameHelper::fullName($screenNames[2])).'</a> ' . xg_text('AND_X_OTHER_PEOPLE',count($screenNames)-3) . ' ';
        }

         ?>
        <span class="message">
        <%= ((XG_App::onMyProfilePage())&&($isProfile)&&($this->_user->screenName == $username))?xg_html('YOU_COMMENTED_ON_VIDEO_TITLE',$href, xnhtmlentities($video->title)):xg_html('X_LINK_COMMENTED_ON_VIDEO_TITLE', $userSet, $href, xnhtmlentities($video->title)) %>
       </span>
       <%= $timeStamp %>
    <?php
    }

    //new music tracks
    if( ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_HOME_TRACK)||
        (($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_TRACK)&&($item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT))
        ){
        $firstTracks = array();
        for($i=0; $i < 4; $i++){
            if ($i >= count($contentIds)) continue;
            $firstTracks[] = XG_Cache::content($contentIds[$i]);
        }
        if($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_HOME_TRACK){
            $item->title    = xg_text('X_NEW_SONGS_ADDED_TO_APPNAME', count($contentIds), $appName);
            $item->my->link = 'http://' . $_SERVER['HTTP_HOST'];
            ?><span class="message"> <%= xnhtmlentities($item->title) %></span><?php
        } else {
            $item->title    = xg_text('Y_ADDED_X_SONGS', count($contentIds), $fullname);
            $item->my->link = 'http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($username);
            ?><span class="message"> <%= ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('YOU_ADDED_X_SONGS', count($contentIds)):xg_html('Y_ADDED_X_SONGS', count($contentIds), $who) %></span><?php
        }
        echo $timeStamp;
        //@TODO generate enclosure if rss
                $this->renderPartial('fragment_thumb_tracks', 'log', array('tracks' => $firstTracks));?>
                <?php
    }

    //new videos
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_VIDEO)&&($item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT)) {
        W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_SecurityHelper.php');
        $firstVideos = array();
        for($i=0; $i < 3; $i++){
            if ($i >= count($contentIds)) continue;
            $video = XG_Cache::content($contentIds[$i]);
            if ( (($isProfile)&&(! $error = Video_SecurityHelper::checkVisibleToCurrentUser($this->_user, $video)))||
                 ((!$isProfile)&&($video->my->visibility == 'all')) ) {
                $firstVideos[] = $video;
            }
        }
        if(count($firstVideos)==0) { throw new Exception("Empty item."); }
        W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
        $item->title    = xg_text('Y_ADDED_X_VIDEOS', count($contentIds), $fullname);
        $item->my->link = (count($contentIds)>1) ?
         W_Cache::getWidget('video')->buildUrl('video', 'listForContributor', array('screenName' => $username)) :
         W_Cache::getWidget('video')->buildUrl('video', 'show', array('id' => $firstVideos[0]->id)) ;
        //@TODO generate enclosure if rss
         ?><span class="message"> <%= ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('YOU_ADDED_X_VIDEOS', count($contentIds)):xg_html('Y_ADDED_X_VIDEOS', count($contentIds), $who) %> <a href="<%= $item->my->link
          %>"><%=  xnhtmlentities((count($contentIds)>1)?xg_text('VIEW_VIDEOS'):xg_excerpt($firstVideos[0]->title,$titleMaxLength)) %></a></span>
          <%= $timeStamp %>
          <?php
        $this->renderPartial('fragment_thumb_videos', 'log', array('videos' => $firstVideos));
    }

    //new photos
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_PHOTO)&&($item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT)) {
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_SecurityHelper.php');
        $firstPhotos = array();
        for($i=0; $i < 4; $i++){
            if ($i >= count($contentIds)) continue;
            $photo = XG_Cache::content($contentIds[$i]);
            if ( (($isProfile)&&(! $error = Photo_SecurityHelper::checkVisibleToCurrentUser($this->_user, $photo)))||
                 ((!$isProfile)&&($photo->my->visibility == 'all')) ) {
                $firstPhotos[] = $photo;
            }
        }
        if(count($firstPhotos)==0) { throw new Exception("Empty item."); }
        $item->title    = xg_text('Y_ADDED_X_PHOTOS', count($contentIds), $fullname);
        $item->my->link = (count($contentIds)>1) ?
            W_Cache::getWidget('photo')->buildUrl('photo', 'listForContributor', array('screenName' => $username)) :
             W_Cache::getWidget('photo')->buildUrl('photo', 'show', array('id' => $firstPhotos[0]->id));
        ?><span class="message"> <%= ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('YOU_ADDED_X_PHOTOS', count($contentIds)):xg_html('Y_ADDED_X_PHOTOS', count($contentIds), $who) %> <a href="<%= $item->my->link
         %>"><%=  xnhtmlentities((count($contentIds)>1)?xg_text('VIEW_PHOTOS'):xg_excerpt($firstPhotos[0]->title,$titleMaxLength)) %></a></span>
         <%= $timeStamp %>
         <?php
         $this->renderPartial('fragment_thumb_photos', 'log', array('photos' => $firstPhotos));
    }

    //new albums
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_ALBUM)&&($item->my->category == XG_ActivityHelper::CATEGORY_NEW_CONTENT)) {
        W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_SecurityHelper.php');
        $firstAlbums = array();
        for($i=0; $i < 4; $i++){
            if ($i >= count($contentIds)) continue;
            $album = XG_Cache::content($contentIds[$i]);
            if ($album->my->hidden != 'Y') {
                $firstAlbums[] = $album;
            }
        }
        if(count($firstAlbums)==0) { throw new Exception("Empty item."); }
        $item->title    = xg_text('Y_ADDED_X_ALBUMS', count($contentIds), $fullname);
        $item->my->link = (count($contentIds)>1) ?
            W_Cache::getWidget('photo')->buildUrl('album', 'listForOwner', array('screenName' => $username)) :
             W_Cache::getWidget('photo')->buildUrl('album', 'show', array('id' => $firstAlbums[0]->id));
        ?><span class="message"> <%= ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('YOU_ADDED_X_ALBUMS', count($contentIds)):xg_html('Y_ADDED_X_ALBUMS', count($contentIds), $who) %> <a href="<%= $item->my->link
         %>"><%=  xnhtmlentities((count($contentIds)>1)?xg_text('VIEW_ALBUMS'):xg_excerpt($firstAlbums[0]->title,$titleMaxLength)) %></a></span>
         <%= $timeStamp %>
         <?php
         $this->renderPartial('fragment_thumb_albums', 'log', array('albums' => $firstAlbums));
    }

    //member joined a group
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_GROUP)&&($item->my->category == XG_ActivityHelper::CATEGORY_CONNECTION)){
        $contentIds = array_unique($contentIds);
        XG_App::includeFileOnce('/lib/XG_GroupHelper.php');
        $group = XG_Cache::content($contentIds[0]);
         ?>
        <span class="message"><?php
        if(count($contentIds) == 1){
            $item->title    = xg_text('X_JOINED_THE_GROUP_Y', $fullname, xg_excerpt($group->title,$titleMaxLength));
            $item->my->link = XG_GroupHelper::buildUrl('groups', 'group', 'show', array('id' => $contentIds[0]));
            $groupLink = '<a href="'.xnhtmlentities($item->my->link) .'">'.xnhtmlentities(xg_excerpt($group->title,$titleMaxLength)).'</a>'; ?>
            <%= ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('YOU_JOINED_THE_GROUP_X', $groupLink):xg_html('X_JOINED_THE_GROUP_Y', $who, $groupLink) %></span><?php
        } else {
            $item->title    = xg_text('X_JOINED_Y_GROUPS', $fullname, count($contentIds));
            $item->my->link = W_Cache::getWidget('groups')->buildUrl('group', 'list'); ?>
            <%= ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('YOU_JOINED_X_GROUPS', count($contentIds)):xg_html('X_JOINED_Y_GROUPS', $who, count($contentIds)) %>. <a href="<%= $item->my->link %>"><%= xg_html('VIEW_GROUPS')%></a></span><?php
        }
        echo $timeStamp;
    }

    //member joined the network
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_PROFILE)&&($item->my->category == XG_ActivityHelper::CATEGORY_CONNECTION)){
        $memberNumber = $item->my->raw(XG_App::widgetAttributeName(W_Cache::getWidget('main'), 'memberNumber'));
        $item->title    = strip_tags(xg_text('X_JOINED_APPNAME', $fullname, $appName));
        $item->my->link = 'http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($username);
        if (XG_App::onMyProfilePage() && $memberNumber && $this->_user->screenName == $username && XG_App::canSeeInviteLinks(XN_Profile::current())) { ?>
            <span class="message"> <%= xg_html('WELCOME_MEMBER_N_OF_APPNAME_INVITE', $who, $memberNumber, xnhtmlentities($appName), 'href="' . xnhtmlentities('/invite') . '"') %></span>
        <?php
        } elseif (XG_App::onMyProfilePage() && $memberNumber) { ?>
            <span class="message"> <%= xg_html('WELCOME_MEMBER_N_OF_APPNAME', $who, $memberNumber, xnhtmlentities($appName)) %></span>
        <?php
        } elseif (XG_App::onProfilePage() && $memberNumber) { ?>
            <span class="message"> <%= xg_html('X_IS_MEMBER_N_OF_APPNAME', $who, $memberNumber, xnhtmlentities($appName)) %></span>
        <?php
        } else {
            $commentWallUrl = xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('comment','list',array('attachedToType' => 'User','attachedTo'=> $username))); ?>
            <span class="message"> <%= xg_html('X_JOINED_APPNAME', $who, $appName, 'href="' . $commentWallUrl .'"', xnhtmlentities($fullname)) %></span>
         <?php
        }
        echo $timeStamp;
    }

    //member updated profile
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_PROFILE)&&($item->my->category == XG_ActivityHelper::CATEGORY_UPDATE)){
        $item->title    = xg_text('XS_PROFILE_CHANGED', $fullname);
        $item->my->link = 'http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($username); ?>
         <span class="message"><%= ((XG_App::onMyProfilePage())&&($isProfile))?xg_html('YOUR_PROFILE_CHANGED'):xg_html('XS_PROFILE_CHANGED', $who) %></span>
         <%= $timeStamp %><?php
    }

    //new chatter on the chatterwall
    if(($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_PROFILE)&&($item->my->category == XG_ActivityHelper::CATEGORY_NEW_COMMENT)){
        $item->title    = xg_text('X_LEFT_A_COMMENT_FOR_Y', $fullname, XG_FullNameHelper::fullName($members[1]));
        $item->my->link = 'http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($members[1]);

        if ((!$isProfile)||($members[1] != $profileOwner)){
            $commentedProfileLink = '<a href="'.xnhtmlentities($item->my->link).'">'.xnhtmlentities(XG_FullNameHelper::fullName($members[1])).'</a>';
        }else {
            $commentedProfileLink = xnhtmlentities(XG_FullNameHelper::fullName($members[1]));
        }
         ?><span class="message"> <?php if((XG_App::onMyProfilePage())&&(!$_GET['viewAsOther'])&&($isProfile)){
             if($this->_user->screenName == $username){
                 echo xg_html('YOU_LEFT_A_COMMENT_FOR_X', $commentedProfileLink);
             }else {
                 echo xg_html('X_LEFT_A_COMMENT_FOR_YOU', '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($username)).'">'.xnhtmlentities($fullname).'</a>');
             }
         }else {
             echo xg_html('X_LEFT_A_COMMENT_FOR_Y', $who, $commentedProfileLink);
         }  ?></span>
         <%= $timeStamp %><?php
    }

    // handle featured items
    if ($item->my->category == XG_ActivityHelper::CATEGORY_FEATURE) {
        $featuredItem = XG_Cache::content($contentIds[0]);
        $featuredItemPartialRenderVars = array(
            'item' => $featuredItem,
            'itemCount' => count($contentIds),
            'fullname' => $fullname,
            'username' => $username,
            'who' => $who,
            'members' => $members,
            'timeStamp' => $timeStamp,
        );

        // @todo determine if this information should live here, or if it should be
        //       available for reuse by refactoring into a method somewhere
        $featuredItemSpecificVars = array(
            XG_ActivityHelper::SUBCATEGORY_TOPIC => array(
                'FORUM_POST',
                'forum/topic/featured',
            ),
            XG_ActivityHelper::SUBCATEGORY_PHOTO => array(
                'PHOTO',
                'photo/photo/listFeatured',
            ),
            XG_ActivityHelper::SUBCATEGORY_VIDEO => array(
                'VIDEO',
                'video/video/listFeatured',
            ),
            XG_ActivityHelper::SUBCATEGORY_GROUP => array(
                'GROUP',
                'groups/group/listFeatured',
            ),
            XG_ActivityHelper::SUBCATEGORY_MEMBER => array(
                'PROFILE',
                'profiles/friend/listFeatured',
            ),
            XG_ActivityHelper::SUBCATEGORY_BLOG_POST => array(
                'BLOG_POST',
                'profiles/blog/list?promoted=1',
            ),
            XG_ActivityHelper::SUBCATEGORY_EVENT => array(
                'EVENT',
                'events/event/listFeatured',
            ),
            XG_ActivityHelper::SUBCATEGORY_ALBUM => array(
                'ALBUM',
                'photo/album/listFeatured',
            ),
            XG_ActivityHelper::SUBCATEGORY_MUSIC => array(
                'MUSIC',
                null,
            ),
            XG_ActivityHelper::SUBCATEGORY_NOTES => array(
                'NOTE',
                'notes/index/featuredNotes',
            ),
        );

        list(
            $featuredItemPartialRenderVars['textType'],
            $featuredItemPartialRenderVars['listLink']
        ) = $featuredItemSpecificVars[$item->my->subcategory];

        // @todo bail out if $featuredItemSpecificVars[$item->my->subcategory] isn't set

        /**
         * @internal Some of the subcategory types need specific extras added to them.
         *           Use this switch statement to add that extra content to the
         *           $featuredItemPartialRenderVars array using the key 'extra' to display
         */
        switch ($item->my->subcategory) {
            case XG_ActivityHelper::SUBCATEGORY_PHOTO:
                W_Cache::getWidget('photo')->includeFileOnce('/lib/helpers/Photo_SecurityHelper.php');
                $totalPhotos = count($contentIds);
                $featuredItemPartialRenderVars['photos'] = array();
                for ($i = 0; $i < 4 && $i < $totalPhotos; $i++) {
                    $photo = XG_Cache::content($contentIds[$i]);
                    if (Photo_SecurityHelper::isViewableOnLatestActivity($photo, $this->_user, $isProfile)) {
                        $featuredItemPartialRenderVars['photos'][] = $photo;
                    }
                }
                break;

            case XG_ActivityHelper::SUBCATEGORY_VIDEO:
                W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_SecurityHelper.php');
                $totalVideos = count($contentIds);
                $featuredItemPartialRenderVars['videos'] = array();
                for ($i = 0; $i < 3 && $i < $totalVideos; $i++) {
                    $video = XG_Cache::content($contentIds[$i]);
                    if (Video_SecurityHelper::isViewableOnLatestActivity($video, $this->_user, $isProfile)) {
                        $featuredItemPartialRenderVars['videos'][] = $video;
                    }
                }
                break;

        }
        $this->renderPartial('fragment_featuredItemLogItem', 'log', $featuredItemPartialRenderVars);
    }

    // handle profile photo changes
    if ($item->my->category == XG_ActivityHelper::CATEGORY_USER_PROFILE && $item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_PROFILE_PHOTO) {
        $this->renderPartial('fragment_profilePhotoChange', 'log', array(
            'contentIds' => $contentIds,
            'timeStamp' => $timeStamp,
        ));
    }

    //network facts and messages
    if($item->my->category == XG_ActivityHelper::CATEGORY_NETWORK){
        $item->my->link = 'http://' . $_SERVER['HTTP_HOST'];
        $item->title    = xg_text('ANNOUNCEMENT');
        if($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_MESSAGE) { ?>
            <h5 class="xg_lightfont"><%= xg_html('ANNOUNCEMENT') %></h5>
            <span class="message"><%= xnhtmlentities(xg_excerpt($item->description,140)) %></span><?php
        }else if($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_MESSAGE_QUESTIONS_UPDATE) { ?>
            <h5 class="xg_lightfont"><%= xg_html('ANNOUNCEMENT') %></h5>
            <span class="message"><%= xg_html('APPNAME_HAS_NEW_PROFILE_QUESTIONS', $appName, 'href="' . xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('settings', 'editProfileInfo')) . '"') %></span><?php
        }else if($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_MESSAGE_NEW_FEATURE) {
            if($itemWidget = W_Cache::getWidget($item->my->widgetName)) {
                if ($item->my->widgetName === 'profiles') {
                    $itemWidgetIndex = $itemWidget->buildUrl('blog', 'list');
                } else {
                    $itemWidgetIndex = $itemWidget->buildUrl('index', 'index');
                }
            }
            ?>
                <h5 class="xg_lightfont"><%= xg_html('ANNOUNCEMENT') %></h5>
            <span class="message"><%= xg_html('APPNAME_NOW_HAS_'.mb_strtoupper($item->my->widgetName), $appName, 'href="' . xnhtmlentities($itemWidgetIndex) . '"' ) %></span><?php
            }else if($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_NETWORK_CREATED && $this->_user->isOwner()) { ?>
                <h5 class="xg_lightfont"><%= xg_html('CONGRATULATIONS') %></h5>
                <span class="message"><%= xg_html('YOU_CREATED_APPNAME', xnhtmlentities($appName)) %></span><?php
            }else if($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_NETWORK_CREATED && ! $this->_user->isOwner()) { ?>
                <h5 class="xg_lightfont"><%= xg_html('ANNOUNCEMENT') %></h5>
                <span class="message"><%= xg_html('USER_CREATED_APPNAME', xnhtmlentities(xg_username(XG_Cache::profiles(XN_Application::load()->ownerName))), xnhtmlentities($appName)) %></span><?php
        }else {
            $item->title    = xg_text('DID_YOU_KNOW');  ?>
            <h5 class="xg_lightfont"><%= xg_html('DID_YOU_KNOW') %></h5> <?php
            if($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_MESSAGE) { ?>
                <span class="message"><%= html_entity_decode($item->description) %></span><?php
            } else if ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_PHOTO_CHAMPION) { ?>
                <span class="message"><%= xg_html('X_HAS_POSTED_THE_MOST_PHOTOS', '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($username)).'">'.xnhtmlentities($fullname).'</a>','<a href="'.xnhtmlentities(W_Cache::getWidget('photo')->buildUrl('photo', 'index')).'">', '</a>') %></span><?php
            } else if ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_VIDEO_CHAMPION) { ?>
                <span class="message"><%= xg_html('X_HAS_POSTED_THE_MOST_VIDEOS', '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($username)).'">'.xnhtmlentities($fullname).'</a>','<a href="'.xnhtmlentities(W_Cache::getWidget('video')->buildUrl('video', 'index')).'">', '</a>') %></span><?php
            } else if ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_MUSIC_CHAMPION) { ?>
                <span class="message"><%= xg_html('X_HAS_POSTED_THE_MOST_MUSIC', '<a href="'.xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . User::quickProfileUrl($username)).'">'.xnhtmlentities($fullname).'</a>') %></span><?php
            } else if ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOP_PHOTO) {
                $photo = XG_Cache::content($contentIds[0]); ?>
                <span class="message"><%= xg_html('X_IS_THE_MOST_POPULAR_PHOTO',  '<a href="'. W_Cache::getWidget('photo')->buildUrl('photo', 'show', array('id' => $photo->id)).'">'.xnhtmlentities($photo->title?xg_excerpt($photo->title,$titleMaxLength):xg_text('UNTITLED')).'</a>') %></span><?php
                $this->renderPartial('fragment_thumb_photos', 'log', array('photos' => array($photo)));
            } else if ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOP_VIDEO) {
                W_Cache::getWidget('video')->includeFileOnce('/lib/helpers/Video_VideoHelper.php');
                $video = XG_Cache::content($contentIds[0]); ?>
                <span class="message"><%= xg_html('X_IS_THE_MOST_POPULAR_VIDEO',  '<a href="'. W_Cache::getWidget('video')->buildUrl('video', 'show', array('id' => $video->id)).'">'.xnhtmlentities($video->title?xg_excerpt($video->title,$titleMaxLength):xg_text('UNTITLED')).'</a>') %></span><?php
                $this->renderPartial('fragment_thumb_videos', 'log', array('videos' => array($video)));
            } else if ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOP_MUSIC) {
                $track = XG_Cache::content($contentIds[0]); ?>
                <span class="message"><%= xg_html('X_IS_THE_MOST_POPULAR_MUSIC', xnhtmlentities($track->my->artist.' '.(($track->my->artist && $track->my->trackTitle) ? ' &mdash;':'').' '.$track->my->trackTitle)) %></span><?php
                $this->renderPartial('fragment_thumb_tracks', 'log', array('tracks' => array($track)));
            } else if ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOP_TOPIC) {
                $topic = XG_Cache::content($contentIds[0]);
                if ($topic->my->groupId) {
                    $link = xnhtmlentities('http://' . $_SERVER['HTTP_HOST'] . '/group/' . Group::idToUrl($topic->my->groupId) . '/forum/topic/show?id=' . $topic->id);
                } else {
                    $link = xnhtmlentities((W_Cache::getWidget('forum')->buildUrl('topic', 'show', array('id' => $topic->id))));
                }
                ?>
                <span class="message"><%= xg_html('X_IS_THE_MOST_POPULAR_TOPIC', '<a href="'.$link.'">'.xg_excerpt($topic->title,$titleMaxLength).'</a>') %></span><?php
            } else if ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOP_BLOGPOST) {
                $blogPost = XG_Cache::content($contentIds[0]); ?>
                <span class="message"><%= xg_html('X_IS_THE_MOST_POPULAR_BLOG_POST', '<a href="'.xnhtmlentities((W_Cache::getWidget('profiles')->buildUrl('blog', 'show', array('id' => $blogPost->id)))).'">'.xg_excerpt($blogPost->title?$blogPost->title:BlogPost::getTextTitle($blogPost),$titleMaxLength).'</a>') %></span><?php
            } else if ($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TODAY_EVENT
                    || $item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TOMORROW_EVENT) {
                $event = XG_Cache::content($contentIds[0]);
                $lnk = '<a href="'.xnhtmlentities(W_Cache::getWidget('events')->buildUrl('event', 'show', array('id' => $event->id))).'">'.xnhtmlentities(xg_excerpt($event->title,20)) . '</a>';
                echo '<span class="message">' . xg_html( $item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_FACT_TODAY_EVENT ? 'EVENT_X_IS_HAPPENING_TODAY' : 'EVENT_X_IS_HAPPENING_TOMORROW', $lnk) . '</span>';
            }
        }
        echo $timeStamp;
    }

    //network facts and messages
    if($item->my->category == XG_ActivityHelper::CATEGORY_GADGET){
        $item->my->link = $item->my->link;
        $item->title    = $item->title;
        //@TODO find a proper javascript: links remover and use it instead of str_replace
        if($item->my->subcategory == XG_ActivityHelper::SUBCATEGORY_MESSAGE) { ?>
            <h5 class="xg_lightfont"><%= xnhtmlentities(xg_excerpt($item->title,140)) %></h5>
            <span class="message"><a href="<%=xnhtmlentities(xg_scrub(str_replace('javascript','',$item->my->link))) %>"><%= xnhtmlentities(xg_excerpt($item->description,140)) %></a></span><?php
        }
        echo $timeStamp;
    }
?>
            </div>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    echo $output;
} catch (Exception $e) {
    ob_end_clean();
    if ($_GET['test_show_bad_activity_log_item_ids']) { echo 'Bad: ' . $item->id; }
    // Don't expose this error to the user (BAZ-4209) [Jon Aquino 2007-08-29]
}
