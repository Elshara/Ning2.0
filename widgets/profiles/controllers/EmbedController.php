<?php

XG_App::includeFileOnce('/lib/XG_Embed.php');
XG_App::includeFileOnce('/lib/XG_LockingCacheController.php');

class Profiles_EmbedController extends XG_LockingCacheController {

    // see http://jira.ninginc.com/browse/BAZ-2659
    // and http://clearspace.ninginc.com/clearspace/docs/DOC-1110/version/1

    //  Default display is blog posts
    public function action_embed2($args) {
        $this->forwardTo('embed2blogposts', NULL, array($args));
    }

    public function action_embed1($args) {
        $this->forwardTo('embed2blogposts', NULL, array($args));
    }

    /**
     * The non-editable title of the page
     */
    public function action_embed3pagetitle($args) {
        $this->embed = $args['embed'];
        $screenName = $this->embed->get('screenName');
        $this->profile = XG_Cache::profiles($screenName);
        $this->pageTitle = $this->embed->isOwnedByCurrentUser() ? xg_text('MY_PAGE') : xg_text('XS_PAGE', xg_username($screenName));
        $this->modulesLinks = array();
        $user = User::load($this->profile);
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
    }

    /**
     * The user-editable title / status for the profile page
     */
    public function action_embed2pagetitle($args) {
        $this->embed = $args['embed'];
        $screenName = $this->embed->getOwnerName();
        $this->profile = XG_Cache::profiles($screenName);
        $titleAttributeName = XG_App::widgetAttributeName($this->_widget, 'pageTitle');
        $user = User::load($this->profile);
        $title = $user->my->{$titleAttributeName};
        if ($title) {
            $this->pageTitle = $title;
        } else {
			$this->pageTitle = $this->embed->isOwnedByCurrentUser() ? xg_text('WELCOME_ADD_YOUR_TITLE') : xg_text('XS_PAGE', xg_username($user->title));
        }
    }

    /** The badge that appears on the top left of the profile page */
    public function action_embed1smallbadge($args) {
        $this->embed = $args['embed'];
        $screenName = $this->embed->get('screenName');
        $this->hideLinks = $this->embed->get('hideLinks');
        $this->profile = XG_Cache::profiles($screenName);
        //  Can the logged in user ban the user displayed?
        if ($this->profile->screenName == XN_Profile::current()->screenName) {
            // The page belongs to the logged in user - don't show ban option
            $this->canBan = FALSE;
        } else {
            $this->canBan = XG_SecurityHelper::currentUserCanDeleteUser(
                    $this->profile->screenName);
        }
        $this->user = User::load($this->profile);
        XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
        $this->friendStatus = XG_ContactHelper::getFriendStatusFor($this->_user->screenName, $screenName);
        $this->isBlocked = BlockedContactList::isSenderBlocked(XN_Profile::current()->screenName, $screenName);
        W_Cache::getWidget('opensocial')->includeFileOnce('/lib/helpers/OpenSocial_GadgetHelper.php');
        $this->openSocialApps = (XG_App::openSocialEnabled() ? OpenSocial_GadgetHelper::getInstalledApps($screenName) : array());
        $this->profileLinks = Profiles_UserHelper::getUserProfileNavigation($this->user);
    }

    public function action_embed1badge() {
        $this->app = XN_Application::load();
        if (XG_SecurityHelper::userIsOwner()) {
            $this->owner = $this->_user;
        }
        else {
            $this->owner = XG_Cache::Profiles($this->app->ownerName);
        }
        if (XG_App::onMyProfilePage()) {
            W_Cache::getWidget('profiles')->dispatch('embed', 'getBadge');
        }
        else {
            W_Cache::getWidget('main')->dispatch('embed', 'getBadge');
        }
        $this->renderNothing();
    }

    /** Profile questions and answers */
    public function action_embed1profileqa($args) {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_ProfileQuestionHelper.php');
        if (count(Profiles_ProfileQuestionHelper::getQuestions(W_Cache::getWidget('profiles'))) < 1 ) {
            return $this->render('blank');
        }
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_ProfileQuestionFormHelper.php');
        $this->embed = $args['embed'];
        $screenName = $args['screenName'] ? $args['screenName'] : $this->embed->get('screenName');
        $this->profile = XG_Cache::profiles($screenName);
        $user = User::load($screenName);
        $this->canSeePrivate = ($this->_user->screenName == $this->profile->screenName) || XG_SecurityHelper::userIsAdmin();
        $qa = Profiles_ProfileQuestionFormHelper::read($user);
        $this->questions = $qa['questions'];
        $this->questionsAndAnswers = $qa['answers'];
        $this->maxEmbedWidth = $args['maxEmbedWidth'];
    }
    public function action_embed1profileqa_iphone($args) {
        $this->action_embed1profileqa($args);
    }

    public function action_embed2profileqa($args) {
        $this->forwardTo('embed1profileqa', NULL, array($args));
    }

    public function action_embed1friends($args) {
        $this->forwardTo('friendsProper', 'embed', array($args, 1));
    }

    public function action_embed2friends($args) {
        $this->forwardTo('friendsProper', 'embed', array($args, 2));
    }

    public function action_friendsProper($args, $columnCount) {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FeedHelper.php');
        $this->embed = $args['embed'];
        $screenName = $this->embed->get('screenName');
        $this->profile = XG_Cache::profiles($screenName);
        $friendInfo = Profiles_UserHelper::findFriendsOf($screenName, 0, 6 * $columnCount);
        $this->friends = $friendInfo['friends'];
        // Load all the profile objects corresponding to all the friends
        $screenNames = array();
        foreach ($this->friends as $friend) {
            $screenNames[$friend->contributorName] = $friend->contributorName;
        }
        $this->friendProfiles = XG_Cache::profiles($screenNames);
        $this->feedUrl = Profiles_FeedHelper::feedUrl($this->_buildUrl('friend','feed',array('user' => $screenName, 'xn_auth' => 'no')));
        if (! $this->friendProfiles && $this->embed->getType() == 'profiles') { return $this->render('blank'); }
    }

    public function action_friendsProper_iphone($args) {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FeedHelper.php');
        $screenName = $args['screenName'];
        $this->profile = XG_Cache::profiles($screenName);
        $this->maxFriends = 12;
        $friendInfo = Profiles_UserHelper::findFriendsOf($screenName, 0, $this->maxFriends);
        $this->friends = $friendInfo['friends'];
        // Load all the profile objects corresponding to all the friends
        $screenNames = array();
        foreach ($this->friends as $friend) {
            $screenNames[$friend->contributorName] = $friend->contributorName;
        }
        $this->userIsOwner = ($layoutOpts['viewAsOther'] === false) && ($this->_user->screenName == $this->profile->contributorName);
        $this->friendProfiles = XG_Cache::profiles($screenNames);
        $this->feedUrl = Profiles_FeedHelper::feedUrl($this->_buildUrl('friend','feed',array('user' => $screenName, 'xn_auth' => 'no')));
        $this->friendRequestCount = null;
        if (! $this->friendProfiles) { return $this->render('blank'); }
    }

    /** Active members */
    public function action_embed1activeMembers($args, $columnCount=1) {
        $this->embed = $args['embed'];
        if (!$this->embed->isOwnedByCurrentUser()) {
                    $this->setLockingCaching(__METHOD__, md5(implode(',', array('/profiles/embed/embed1activeMembers',
                                $columnCount,
                                XG_App::canSeeInviteLinks($this->_user),
                                $this->embed->get('displaySet'),
                                $this->embed->get('sortSet'),
                                $this->embed->get('rowsSet'),
                                $args['maxEmbedWidth']))), 300);
        }

        // When adding a new option to this embed, be sure to add it to the md5 hash above (BAZ-5679) [Jon Aquino 2008-01-04]

        $this->display_options = array(array('label' => xg_text('SMALL_THUMBNAILS'), 'value' => 'small'), array('label' => xg_text('LARGE_THUMBNAILS'), 'value' => 'large'));
        $this->sort_options = array( array('label' => xg_text('RECENTLY_JOINED'), 'value' => 'recent'), array('label' => xg_text('MOST_POPULAR_MEMBERS'), 'value' => 'popular'), array('label' => xg_text('FEATURED'), 'value' => 'featured'));
        $this->setValuesUrl = $this->_buildUrl('embed', 'membersSetValues', array('id' => $this->embed->getLocator(), 'maxEmbedWidth' => $args['maxEmbedWidth'], 'xn_out' => 'json', 'columnCount' => $columnCount, 'sidebar' => XG_App::isSidebarRendering() ? '1' : '0'));
        $this->embed->set('displaySet'  , $this->embed->get('displaySet')           ? $this->embed->get('displaySet')   : 'small'   );
        $this->embed->set('sortSet'     , $this->embed->get('sortSet')              ? $this->embed->get('sortSet')      : 'popular' );
        $this->embed->set('rowsSet'     , (!is_null($this->embed->get('rowsSet')))  ? $this->embed->get('rowsSet')      : 4         );
        $this->smallthumbs = ($this->embed->get('displaySet') =='small');
        //20 thumbnails is not allowed anymore (BAZ-2799, BAZ-4894, BAZ-5062)
        if ($this->embed->get('rowsSet') > 5) { $this->embed->set('rowsSet', 5); }
        $rows_numbers = array(0,1,2,3,4,5);
        if ($columnCount > 1) { // BAZ-3866 [Jon Aquino 2007-08-14]
            if ($this->embed->get('rowsSet') > 3) { $this->embed->set('rowsSet', 3); }
            $rows_numbers = array(0,1,2,3);
        }
        $this->rows_options = array();
        foreach ($rows_numbers as $i) {
            $this->rows_options[] = array('label' => (string)($i), 'value' => (string)($i));
        }
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
        $this->usersPerRow = $this->usersPerRow($this->embed, $args['maxEmbedWidth']);
        $membersCount = $this->embed->get('rowsSet') * $this->usersPerRow;
        $this->inviteUrl = XG_App::canSeeInviteLinks($this->_user) ? '/invite' : null;
        $this->viewAllUrl = $this->_buildUrl('members','');
        if ($membersCount>0) {
            if($this->embed->get('sortSet') == 'recent') {
                $this->activeProfiles = XG_Cache::profiles(Profiles_UserHelper::getActiveUsers($membersCount,'createdDate'));
            } elseif($this->embed->get('sortSet') == 'featured') {
                $this->activeProfiles = XG_Cache::profiles(Profiles_UserHelper::getPromotedUsers($membersCount));
            } else {
                $this->activeProfiles = XG_Cache::profiles(Profiles_UserHelper::getActiveUsers($membersCount,'updatedDate'));
            }
        } else {
            $this->activeProfiles = array();
        }
    }

    public function action_embed2activeMembers($args) {
        $this->forwardTo('embed1activeMembers', NULL, array($args, 2));
    }

    /**
     * Displays the body and footer for a Members module.
     * Called by the profiles widget and the groups widget.
     *
     * @param $args array  configuration parameters: profiles (array of XN_Profiles),
     *         smallthumbs (whether to use small thumbnails),
     *         usersPerRow (number of avatars per row),
     *         inviteUrl (URL for the Invite More link, or null to hide the link),
     *         viewAllUrl (URL for the View All link, or null to hide the link)
     *         sortSet (the type of sort/filter that's being performed)
     *         user (the user object, for main page promotion)
     */
    public function action_membersBodyAndFooter($args) {
        $this->activeProfiles = $args['profiles'];
        $this->smallthumbs = $args['smallthumbs'];
        $this->usersPerRow = $args['usersPerRow'];
        $this->inviteUrl = $args['inviteUrl'];
        $this->viewAllUrl = $args['viewAllUrl'];
        $this->sortSet = $args['sortSet'];
        $this->user = $args['user'];
    }

    public function action_embed1blogposts($args) {
        $this->forwardTo('embed2blogposts', NULL, array($args));
    }

    /** Blog posts
     *
     * Regular user can have posts they've recently added or posts across the site
     * Admin can have promoted posts or posts across the site -- title is 'Blogs'
    */
    public function action_embed2blogposts($args) {
        $this->embed = $args['embed'];
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FeedHelper.php');
        $this->display_options = array(
            array('label' => xg_text('DETAIL_VIEW'), 'value' => 'detail'),
            array('label' => xg_text('TITLES_ONLY'), 'value' => 'titles')
        );
        $this->maxEmbedWidth = $args['maxEmbedWidth'];
        $this->embedLayoutType = $this->embed->getType();
        if ($this->embedLayoutType == 'homepage') {
            $this->sort_options = array(
                array('label' => xg_text('RECENTLY_ADDED_POSTS'),   'value' => 'recent'),
                array('label' => xg_text('MOST_POPULAR'),           'value' => 'popular2'),
                array('label' => xg_text('POPULAR_POSTS'),          'value' => 'popular'),
                array('label' => xg_text('PROMOTED_POSTS'),         'value' => 'promoted'),
                array('label' => (XG_SecurityHelper::userIsOwner()?xg_text('MY_POSTS_ONLY'):xg_text('OWNER_POSTS_ONLY')), 'value' => 'owner')
            );
        }
        $this->posts_options = array();
        $options = array(0, 1, 2, 3, 4, 5, 10, 20);
        foreach ($options as $option) {
            $this->posts_options[] = array('label' => (string)($option), 'value' => (string)($option));
        }
        $this->embedSelected = mb_strlen($this->embed->get('selected')) ? $this->embed->get('selected') : 'recent';
        $this->embed->set('displaySet'  , $this->embed->get('displaySet')           ? $this->embed->get('displaySet')   : 'detail'   );
        $this->embed->set('postsSet'     , (!is_null($this->embed->get('postsSet')))  ? $this->embed->get('postsSet')      : 5         );

        $screenName = $this->embed->get('screenName');
        $this->profile = mb_strlen($screenName) ? XG_Cache::profiles($screenName) : null;
        $info = $this->getPostInfo(
            $this->embedLayoutType,
            $this->embedSelected,
            $this->embed,
            $this->profile,
            $this->embed->isOwnedByCurrentUser() && $this->embed->get('postsSet') == 0 ? 1 : $this->embed->get('postsSet')
        );
        $this->postInfo = $info['postInfo'];
        $this->feedUrl = $info['feedUrl'];
        $this->archiveUrl = $info['archiveUrl'];
        $this->embedTitle = $info['embedTitle'];
        // Only show promotion links with each post if it's the homepage
        // and we're showing promoted posts
        // On profile pages, hide promotion links if the current user isn't
        // the layout owner to hide them on 'view as others see it' view
        $this->showPromotionLinks = $info['showPromotionLinks'];
        if (($this->embedLayoutType == 'profiles') && XG_SecurityHelper::userIsAdmin($this->profile) && (! $this->embed->isOwnedByCurrentUser())) {
            $this->showPromotionLinks = false;
        }

        $this->posts = $this->postInfo['posts'];
        if ((! $this->posts && $this->embed->getType() == 'profiles') || (! $this->posts && ! $this->embed->isOwnedByCurrentUser()) || (!$this->embed->isOwnedByCurrentUser() && $this->embed->get('postsSet') == 0)) { return $this->renderNothing(); }

        $this->embedUrl = $this->_buildUrl('embed','blogpostsSetValues', array('id' => $this->embed->getLocator(), 'xn_out' => 'json', 'maxEmbedWidth' => $this->maxEmbedWidth, 'sidebar' => XG_App::isSidebarRendering() ? '1' : '0'));
        $this->embedUpdateUrl = $this->_buildUrl('embed','blogpostsUpdateEmbed', array('id' => $this->embed->getLocator(), 'xn_out' => 'json'));
        XG_App::includeFileOnce('/lib/XG_SecurityHelper.php');
        $this->showCreateLink = XG_SecurityHelper::currentUserCanSeeAddContentLink($this->embed, $this->posts);

        // BAZ-4889: Preload user info
        XG_Cache::profiles($this->posts);
    }

    /** Update blog posts module body without changing embed settings - only called for Frink drop updates */
    public function action_blogpostsUpdateEmbed() {
        // do not set embed values
        $this->blogpostsSetValuesAndUpdateEmbed(false);
    }

    /** Configuring blog posts */
    public function action_blogpostsSetValues() {
        // set embed values
        $this->blogpostsSetValuesAndUpdateEmbed(true);

        // invalidate admin sidebar if necessary
        if ($_GET['sidebar']) {
            XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
            XG_LayoutHelper::invalidateAdminSidebarCache();
        }
    }

    /**
     * Optionally set embed values, and return the updated embed
     * xn_out must be set to 'json'
     *
     * @param $updateSettings   boolean     If true, updates the embed settings from $_POST values
     */
    private function blogpostsSetValuesAndUpdateEmbed($updateSettings = false) {
        try {
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_FeedHelper.php');
            if (! isset($_GET['id'])) {
                throw new Exception("No embed locator provided");
            }
            $embed = XG_Embed::load($_GET['id']);
            if (! $embed->isOwnedByCurrentUser()) {
                throw new Exception("You are not the embed owner!");
            }
            if ($updateSettings) {
                $embed->set('selected', $_POST['sortSet']);
                $embed->set('postsSet', (float)$_POST['postsSet']);
                $embed->set('displaySet', $_POST['displaySet']);
                $embed->set('screenName', $embed->get('screenName')?$embed->get('screenName'):$this->_user->screenName);
            }
            $screenName = $embed->get('screenName');
            $profile = mb_strlen($screenName) ? XG_Cache::profiles($screenName) : null;
            $info = $this->getPostInfo($embed->getType(), $embed->get('selected'), $embed, $profile, $embed->get('postsSet'));
            $this->embedTitle = $info['embedTitle'];
            $maxEmbedWidth = XG_Embed::getValueFromPostGet('maxEmbedWidth');
            list($junk, $this->moduleBodyHtml) = W_Cache::current('W_Widget')->capture('embed','blogpostsBody', array($info['postInfo']['posts'], $embed->isOwnedByCurrentUser(), $info['archiveUrl'], $info['feedUrl'], $embed, $info['showPromotionLinks'], $maxEmbedWidth));
        } catch (Exception $e) {
            $this->message = $e->getMessage();
        }
    }

    public function action_blogpostsBody($posts, $showCreateLink, $archiveUrl, $feedUrl, $embed, $showPromotionLinks, $maxEmbedWidth) {
        $this->posts = $posts;
        $this->showCreateLink = $showCreateLink;
        $this->archiveUrl = $archiveUrl;
        $this->feedUrl = $feedUrl;
        $this->embed = $embed;
        $this->showPromotionLinks = $showPromotionLinks;
        $this->maxEmbedWidth = $maxEmbedWidth;
    }

    private function getPostInfo($layoutType, $embedSelected, $embed, $profile = null, $end = 5) {
        $r = array();
        $r['embedTitle'] = xg_text('BLOG_POSTS');
        if ($end == 0) return $r;
        if ($layoutType == 'profiles') {
            // Load up the five most recent blog posts this user can see
            if ($embedSelected == 'site') {
                $r['postInfo'] = BlogPost::find(array('my->publishStatus' => 'publish'), 0, $end);
                $r['feedUrl'] = Profiles_FeedHelper::feedUrl($this->_buildUrl('blog','feed',array('xn_auth' => 'no')));
                $r['archiveUrl'] = $this->_buildUrl('blog','list');
                $r['embedTitle'] = xg_text('BLOG_POSTS');
                $r['showPromotionLinks'] = true;
            } else {
                if (! $profile) {
                    throw new Exception('Profile required to load member blog posts.');
                }
                $r['postInfo'] = BlogPost::find(array('my->publishStatus' => 'publish', 'contributorName' => $profile->screenName), 0, $end);
                $r['feedUrl'] = Profiles_FeedHelper::feedUrl($this->_buildUrl('blog','feed',array('user' => $profile->screenName, 'xn_auth' => 'no')));
                $r['archiveUrl'] = $this->_buildUrl('blog','list',array('user' => $profile->screenName));
                $r['embedTitle'] = $embed->isOwnedByCurrentUser() ? xg_text('MY_BLOG') : xg_text('XS_BLOG', ucfirst(xg_username($profile)));
                $r['showPromotionLinks'] = true;
            }
        } else { //homepage
            switch ($embedSelected) {
                case 'promoted':
                    $r['postInfo'] = BlogPost::find(array('my->publishStatus' => 'publish', 'promoted' => true, 'ignoreVisibility' => true, 'my.visibility' => 'all'), 0, $end);
                    $r['feedUrl'] = Profiles_FeedHelper::feedUrl($this->_buildUrl('blog','feed',array('promoted' => 1, 'xn_auth' => 'no')));
                    $r['archiveUrl'] = $this->_buildUrl('blog','list',array('promoted' => 1));
                    $r['showPromotionLinks'] = true;
                    break;
                case 'owner':
                    $this->app = XN_Application::load();
                    $r['postInfo'] = BlogPost::find(array('my->publishStatus' => 'publish', 'contributorName' => $this->app->ownerName, 'ignoreVisibility' => true, 'my.visibility' => 'all'), 0, $end);
                    $r['feedUrl'] = Profiles_FeedHelper::feedUrl($this->_buildUrl('blog','feed',array('user' => $this->app->ownerName, 'xn_auth' => 'no')));
                    $r['archiveUrl'] = $this->_buildUrl('blog','list',array('user' => $this->app->ownerName));
                    $r['showPromotionLinks'] = true;
                    break;
                case 'popular':
                    $r['postInfo'] = BlogPost::find(array('my->publishStatus' => 'publish', 'ignoreVisibility' => true, 'my.visibility' => 'all'), 0, $end, 'my->xg_profiles_commentCount', 'desc');
                    $r['feedUrl'] = Profiles_FeedHelper::feedUrl($this->_buildUrl('blog','feed',array('xn_auth' => 'no')));
                    $r['archiveUrl'] = $this->_buildUrl('blog','list');
                    $r['showPromotionLinks'] = true;
                    break;
                case 'popular2':
                    $r['postInfo'] = BlogPost::find(array('my->publishStatus' => 'publish', 'ignoreVisibility' => true, 'my.visibility' => 'all'), 0, $end, 'my->popularityCount', 'desc');
                    $r['feedUrl'] = Profiles_FeedHelper::feedUrl($this->_buildUrl('blog','feed',array('xn_auth' => 'no')));
                    $r['archiveUrl'] = $this->_buildUrl('blog','list');
                    $r['showPromotionLinks'] = true;
                    break;
                case 'recent':
                default:
                    $r['postInfo'] = BlogPost::find(array('my->publishStatus' => 'publish', 'ignoreVisibility' => true, 'my.visibility' => 'all'), 0, $end);
                    $r['feedUrl'] = Profiles_FeedHelper::feedUrl($this->_buildUrl('blog','feed',array('xn_auth' => 'no')));
                    $r['archiveUrl'] = $this->_buildUrl('blog','list');
                    $r['showPromotionLinks'] = false;
                    break;
            }
        }
        return $r;
    }

    /** Chatterwall display */
    public function action_embed2chatterwall($args) {
        $this->embed = $args['embed'];
        if (is_null($this->embed->get('itemCount'))) { $this->embed->set('itemCount',  10); }
        $screenName = $this->embed->get('screenName');
        $this->profile = XG_Cache::profiles($screenName);
        $user = User::load($this->profile);
        if ($this->embed->isOwnedByCurrentUser()) {
            // If the current user is the owner of the embed, also set things
            // up so they can edit settings (change chatter moderation) (BAZ-960)
            $moderationAttributeName = XG_App::widgetAttributeName($this->_widget, 'moderateChatters');
            $this->userModeratesChatters = ($user->my->{$moderationAttributeName} == 'Y') ? 'Y' : 'N';
            $this->embedUrl = $this->_buildUrl('embed', 'setValuesForChatters', array('id' => $this->embed->getLocator(), 'xn_out' => 'json'));
        }
        list($this->moduleBodyAndFooterHtml, $this->chatterInfo) = $this->chatterwallModuleBodyAndFooterHtml($user, $this->embed);
    }

    /**
     * Returns HTML for the body and footer of the Chatter module.
     *
     * @param $user W_Content  the User who owns the chatter wall
     * @param $embed XG_Embed containing the state of the chatter module
     * @return array  the HTML, followed by an array of 'comments' and 'numComments'
     */
    private function chatterwallModuleBodyAndFooterHtml($user, $embed) {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
        $chatterInfo = Comment::getCommentsFor($user->id, 0, $embed->get('itemCount'), $embed->isOwnedByCurrentUser() ? NULL : 'Y', 'createdDate', 'desc');
        if ($embed->isOwnedByCurrentUser() && $chatterInfo['numComments']) {
            XG_App::includeFileOnce('/lib/XG_ContactHelper.php');
            $friendStatus = XG_ContactHelper::getFriendStatusFor($user->title, array_keys(User::screenNames($chatterInfo['comments'])));
        }
        XG_Cache::profiles($chatterInfo['comments']); // BAZ-4878: pre-load all the user/profile info for the chatter owners
        ob_start(); ?>
        <div class="xg_module_body xj_ajax">
            <?php
            $this->renderPartial('fragment_chatter_list', 'chatter', array('chatters' => $chatterInfo['comments'],
                    'permitLinks' => NULL, 'isMyPage' => $user->title == XN_Profile::current()->screenName, 'responder' => $user->title,
                    'friendStatus' => $friendStatus)); ?>
        </div>
        <?php
        if ($chatterInfo['numComments'] > $embed->get('itemCount')) { ?>
            <div class="xg_module_foot xj_ajax">
                <p class="right"><a href="<%= xnhtmlentities($this->_buildUrl('comment','list',array('attachedToType' => 'User', 'attachedTo' => $user->title))) %>"><%= xg_html('VIEW_ALL_COMMENTS') %></a></p>
            </div>
        <?php
        }
        $html = trim(ob_get_contents());
        ob_end_clean();
        return array($html, $chatterInfo);
    }

    /**
     * Configures the chatter-wall module. The new HTML will be in the moduleBodyAndFooterHtml property of the JSON output.
     *
     * Expected GET parameters:
     *     id - The embed instance ID, used to retrieve the module data
     *     xn_out - "json"
     *
     * Expected POST parameters:
     *     itemCount - Number of comments to display
     */
    public function action_setValuesForChatters() {
        XG_App::includeFileOnce('/lib/XG_Embed.php');
        XG_HttpHelper::trimGetAndPostValues();
        $embed = XG_Embed::load($_GET['id']);
        if (! $embed->isOwnedByCurrentUser()) { throw new Exception('Not embed owner (667715777)'); }
        $embed->set('itemCount', intval($_POST['itemCount']));
        if ($_POST['moderate'] != 'Y' && $_POST['moderate'] != 'N') { throw new Exception('Assertion failed (1012545440)'); }
        $user = User::load($this->_user);
        $user->my->set(XG_App::widgetAttributeName($this->_widget, 'moderateChatters'), $_POST['moderate']);
        $user->save();
        list($this->moduleBodyAndFooterHtml) = $this->chatterwallModuleBodyAndFooterHtml($user, $embed);
    }

    /** @deprecated 3.2  BAZ-7105 */
    public function action_embed3welcome($args) {
    }

    public function action_membersSetValues() {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
        $embed = XG_Embed::load($_GET['id']);
        if (! $embed->isOwnedByCurrentUser()) { throw new Exception('Not embed owner.'); }
        $embed->set('displaySet', $_POST['displaySet']  );
        $embed->set('sortSet'   , $_POST['sortSet']     );
        $embed->set('rowsSet'   , $_POST['rowsSet']     );
        $this->smallthumbs = ($embed->get('displaySet') =='small');
        $this->usersPerRow = $this->usersPerRow($embed, $_GET['maxEmbedWidth']);
        $membersCount = $embed->get('rowsSet') * $this->usersPerRow;
        if($membersCount>0) {
            if($embed->get('sortSet') == 'recent') {
                $this->activeProfiles = XG_Cache::profiles(Profiles_UserHelper::getActiveUsers($membersCount,'createdDate'));
            } elseif($embed->get('sortSet') == 'featured') {
                $this->activeProfiles = XG_Cache::profiles(Profiles_UserHelper::getPromotedUsers($membersCount));
            } else {
                $this->activeProfiles = XG_Cache::profiles(Profiles_UserHelper::getActiveUsers($membersCount,'updatedDate'));
            }
        } else {
            $this->activeProfiles = array();
        }
        ob_start();
        $this->_widget->dispatch('embed', 'membersBodyAndFooter', array(array(
                'profiles' => $this->activeProfiles, 'smallthumbs' => $this->smallthumbs, 'usersPerRow' => $this->usersPerRow,
                'inviteUrl' => XG_App::canSeeInviteLinks($this->_user) ? '/invite' : null,
                'sortSet' => $embed->get('sortSet'),
                'viewAllUrl' => $this->_buildUrl('members',''))));
        $this->moduleBodyAndFooterHtml = trim(ob_get_contents());
        ob_end_clean();

        // invalidate admin sidebar if necessary
        if ($_GET['sidebar']) {
            XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
            XG_LayoutHelper::invalidateAdminSidebarCache();
        }
    }

    /**
     * Returns the number of avatars to display per row
     *
     * @param $embed XG_Embed  Stores the module data.
     * @param $maxEmbedWidth integer  the maximum width for <embed>s, in pixels
     * @return integer  the number of pictures to show on each row
     */
    protected function usersPerRow($embed, $maxEmbedWidth) {
        $columnWidth = $embed->get('displaySet') == 'small' ? 53 : 96;
        return floor($maxEmbedWidth / $columnWidth);
    }

    public function action_error() {
        $this->render('blank');
    }

    /**
     * Displays a module promoting profile badges
     */
    public function action_getBadge() {}

    /* For actions outside of the profiles mozzle (XG_AppearanceTemplateHelper) that
     * need to render the navigation.
     */
    public function action_navigation($screenName) {
        $this->screenName = $screenName;
    }


}
