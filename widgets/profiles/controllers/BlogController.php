<?php

class Profiles_BlogController extends W_Controller {

    const POST_CACHE_LIFETIME = 1800; /* 30 minutes */
    const DEFAULT_BLOG_PAGE_SIZE = 20; /* default page size 20 entries */

    protected function _before() {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_HtmlHelper.php');
    }

    public function action_overridePrivacy($action) {
        //feeds implement their own privacy mechanism
        return (! XG_App::appIsPrivate() && $action == 'feed');
    }

    /**
     * Display the form for creating a new blog post
     *
     * @todo Ideally this handles the form for a new blog post as well
     * as the form, populated, for editing an existing blog post
     */
    public function action_new() {
        /* You must be signed in and a member to post a blog post */
        XG_App::enforceMembership('blog','new');

        $this->months = self::getMonths();
        $this->hours = array(); foreach (range(1,12) as $i) { $this->hours[$i] = $i; }
        $this->minutes = array(); foreach (array(0,30) as $i) { $this->minutes[$i] = sprintf('%02d',$i); }
        $this->ampm = array('AM' => xg_text('AM'), 'PM' => xg_text('PM'));
        $this->selectedMood = '';
        // @todo: Use self::formDefaults [Jon Aquino 2007-04-05]
        $defaults = array(
            'post_title' => $_REQUEST['post_title'],
            'post_body' => $_REQUEST['post_body'],
        );
        list($hour,$min,$defaults['post_month'],$defaults['post_day'],$defaults['post_year']) = explode(',',xg_date('G,i,n,j,Y'));
        if ($hour >= 12) {
            $defaults['post_ampm'] = 'PM';
            $defaults['post_hour'] = ($hour == 12) ? $hour : $hour - 12;
        }
        else {
            $defaults['post_ampm'] = 'AM';
            $defaults['post_hour'] = ($hour == 0) ? $hour + 12 : $hour;
        }
        $defaults['post_min'] = 30 * floor($min / 30);

        $defaults['post_when'] = 'now';
        $this->post_when_timedate_class = ($defaults['post_when'] == 'now') ? 'disabled' : '';
        $user = User::load($this->_user->screenName);
        $defaults['post_add_comment_permission'] = $user->my->addCommentPermission;
        $defaults['post_privacy'] = $user->my->defaultVisibility;
        $this->form = new XNC_Form($defaults);

        $this->showDraftButton = true;
        $this->showDeleteButton = false;

        // We need this to display the timezone name properly
        $mainWidget = W_Cache::getWidget('main');
        $mainWidget->includeFileOnce('/lib/helpers/Index_TimeHelper.php');
        $this->timezone = Index_TimeHelper::offsetToTimezoneName($mainWidget->config['tzOffset'], $mainWidget->config['tzUseDST']);

        // Does the user have blog post moderation turned on or off?
        $moderationAttributeName = XG_App::widgetAttributeName($this->_widget, 'moderateBlogComments');
        $this->commentsAreModerated = ($user->my->{$moderationAttributeName} == 'Y');

        $this->formAction = $this->_buildUrl('blog','create');

        // What should the page title be?
        $this->pageTitle = xg_text('ADD_NEW_BLOG_POST');
        $this->pageHeadline = xg_text('ADD_NEW_BLOG_POST');
        $this->editExistingPost = false;
        self::selectAppropriateTab(true);
    }

    /**
     * Display the form for editing an existing post. This action has no template
     * of its own, but re-uses the 'new' template
     */
    public function action_edit() {
        XG_App::enforceMembership('blog','edit',isset($_GET['id']) ? ('?id=' . $_GET['id']) : '');
        try {
            if (! isset($_GET['id'])) { throw new Exception("No post specified to edit"); }
            $filters = array('id' => $_GET['id'],
                             'contributorName' => $this->_user->screenName);
            $postInfo = BlogPost::find($filters, 0, 1);
            if ($postInfo['numPosts'] != 1) { throw new Exception("No matching post found."); }
            $this->post = $postInfo['posts'][0];
            $this->months = self::getMonths();
            $this->hours = array(); foreach (range(1,12) as $i) { $this->hours[$i] = $i; }
            $this->minutes = array(); foreach (array(0,30) as $i) { $this->minutes[$i] = sprintf('%02d',$i); }
            $this->ampm = array('AM' => xg_text('AM'), 'PM' => xg_text('PM'));
            $this->selectedMood = $this->post->my->mood;

            $defaults = self::formDefaults($this->post, User::load($this->post->contributorName));
            $this->post_when_timedate_class = ($defaults['post_when'] == 'now') ? 'disabled' : '';

            if ($this->post->my->publishStatus == 'draft') {
                $this->showDraftButton = true;
            } else {
                $this->showDraftButton = false;
            }
            $this->showDeleteButton = true;

            // We need this to display the timezone name properly
            $mainWidget = W_Cache::getWidget('main');
            $mainWidget->includeFileOnce('/lib/helpers/Index_TimeHelper.php');
            $this->timezone = Index_TimeHelper::offsetToTimezoneName($mainWidget->config['tzOffset'], $mainWidget->config['tzUseDST']);

            // Does the user have blog post moderation turned on or off?
            $user = User::load($this->_user->screenName);
            $moderationAttributeName = XG_App::widgetAttributeName($this->_widget, 'moderateBlogComments');
            $this->commentsAreModerated = ($user->my->{$moderationAttributeName} == 'Y');

            // What should the page title be?
            // Inherit from either submitted post variable (coming back from preview page) or the title
            // in the content object.
            if (isset($_POST['targetAction']) && ($_POST['targetAction'] == 'update')) {
                if (isset($_POST['post_title']) && mb_strlen(trim($_POST['post_title']))) {
                    $titleFragment = $_POST['post_title'];
                } else {
                    $titleFragment = strip_tags($_POST['post_body']);
                    if (mb_strlen($titleFragment) > 50) {
                        $titleFragment = mb_substr($titleFragment, 0, 50) . '…';
                    }
                }
                $this->pageTitle = xg_text('EDIT_POST_COLON') . $titleFragment;
            } else {
                $this->pageTitle = xg_text('EDIT_POST_COLON') . BlogPost::getTextTitle($this->post, 50);
            }
            $this->pageHeadline = xg_text('EDIT_POST');

            $this->form = new XNC_Form($defaults);
            $this->formAction = $this->_buildUrl('blog','update',array('id' => $this->post->id));
            self::selectAppropriateTab(true);
            $this->editExistingPost = true;
            $this->render('new');

        } catch (Exception $e) {
            error_log("Can't edit: {$e->getMessage()}");
            $this->redirectTo('list');
        }
    }

    /**
     * Returns the attributes of the given BlogPost as an array, suitable for XNC_Form defaults.
     *
     * @param $post XN_Content|W_Content  The BlogPost
     * @param $postOwner XN_Content|W_Content  The User object for author of the BlogPost
     * @return array  The default values for the form
     */
    protected static function formDefaults($post, $postOwner) {
        $defaults = array();
        if ($post->my->publishStatus == 'draft' && $post->my->publishWhen == 'now') {
            $defaults['post_when'] = 'now';
            list($hour,$min,$defaults['post_month'],$defaults['post_day'],$defaults['post_year']) = explode(',',xg_date('G,i,n,j,Y'));
        } else {
            $defaults['post_when'] = 'later';
            list($hour,$min,$defaults['post_month'],$defaults['post_day'],$defaults['post_year']) = explode(',',xg_date('G,i,n,j,Y', strtotime($post->my->publishTime)));
        }
        if ($hour >= 12) {
            $defaults['post_ampm'] = 'PM';
            $defaults['post_hour'] = ($hour == 12) ? $hour : $hour - 12;
        }
        else {
            $defaults['post_ampm'] = 'AM';
            $defaults['post_hour'] = ($hour == 0) ? $hour + 12 : $hour;
        }
        $defaults['post_min'] = 30 * floor($min / 30);
        $defaults['post_privacy'] = $post->my->visibility;
        $defaults['post_add_comment_permission'] = BlogPost::getAddCommentPermission($post, $postOwner);
        $defaults['post_title'] = $post->title;
        $defaults['post_body'] = BlogPost::upgradeDescriptionFormat($post->description, $post->my->format);
        $defaults['post_mood'] = $post->my->mood;
        if ($post->id) {
            XG_App::includeFileOnce('/lib/XG_TagHelper.php');
            $tags = XG_TagHelper::getTagsForObjectAndUser($post->id, XN_Profile::current()->screenName);
            $defaults['tags'] = XG_TagHelper::implode(XN_Tag::tagNamesFromTags($tags));
        }
        return $defaults;
    }

    /** Save an edit to a blog post */
    public function action_update() {
        /* If you get here without being a member, go back to the post form */
        XG_App::enforceMembership('blog','new');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirectTo('new');
            return;
        }
        try {
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_CacheHelper.php');
            if (! isset($_GET['id'])) { throw new Exception("No post specified to edit"); }
            $filters = array('id' => $_GET['id']);
            $postInfo = BlogPost::find($filters, 0, 1);
            if ($postInfo['numPosts'] != 1) { throw new Exception("No matching post found."); }
            $this->post = $postInfo['posts'][0];

            /** If the action was to delete the post, don't bother updating from $_POST data,
             * just delete it and redirect */
            if ($_POST['post_action'] == 'delete') {
                if (! XG_SecurityHelper::userIsAdminOrContributor($this->_user, $this->post)) {
                    throw new Exception("User not authorized to delete this post.");
                }
                $contributorName = $this->post->contributorName;
                $user = User::load($this->_user);
                $this->_widget->includeFileOnce('/lib/helpers/Profiles_BlogArchiveHelper.php');
                Profiles_BlogArchiveHelper::removePostFromArchiveIfEligible($user, $this->post);
                BlogPost::remove($this->post);
                $user->save();
                $url = "http://{$_SERVER['HTTP_HOST']}/profile/" . urlencode(User::profileAddress($contributorName));
                header("Location: $url");
                exit();
            }

            if (! XG_SecurityHelper::userIsContributor($this->_user, $this->post)) {
                throw new Exception("User not authorized to update this post.");
            }
            $data = self::parseBlogPostFormSubmission($_POST);

            // IE 6 doesn't handle <button/> elements with the same name and different values properly
            // See http://www.thescripts.com/forum/threadnav97210-2-10.html
            if ($_POST['post_action'] == 'preview') {
                // Update the post object, don't save it, and display it
                // in preview mode
                BlogPost::update($this->post, $data);
                $this->forwardTo('preview','blog', array('update', $this->post));
                return;
            }
            else if ($_POST['post_action'] == 'draft') {
                // If it's being saved as a draft, no need to update archive status,
                // just save and send to the show page
                BlogPost::update($this->post, $data);
                XG_App::includeFileOnce('/lib/XG_TagHelper.php');
                XG_TagHelper::updateTagsAndSave($this->post, $_POST['tagString']);
                $this->redirectTo('show','blog','?id='.$this->post->id);
                return;
            }
            else {
                if ($data['publishStatus'] == 'publish' && $this->post->my->publishStatus !== 'publish') {
                    $this->_widget->includeFileOnce('/lib/helpers/Profiles_BlogPostingHelper.php');
                    Profiles_BlogPostingHelper::logActivity($this->post);
                    Profiles_BlogPostingHelper::sendPings($this->post);
                }
                $this->_widget->includeFileOnce('/lib/helpers/Profiles_BlogArchiveHelper.php');
                $user = User::load($this->_user);
                Profiles_BlogArchiveHelper::removePostFromArchiveIfEligible($user, $this->post, false);
                BlogPost::update($this->post, $data);
                Profiles_BlogArchiveHelper::addPostToArchiveIfEligible($user, $this->post);
                $user->save(); // Save the updated archive list

                XG_App::includeFileOnce('/lib/XG_TagHelper.php');
                XG_TagHelper::updateTagsAndSave($this->post, $_POST['tags']);

                // BAZ-4324: send you to the blog detail page after post (+ BAZ-4545)
                $this->redirectTo('show','blog',array('id' => $this->post->id));
                return;
            }
        } catch (Exception $e) {
            // @todo show the edit page again
            error_log("Can't save edit: " . $e->getMessage());
        }
    }

    // handler for the "quick post" feature
    public function action_createQuick() { # void
        $this->action_create();
        $this->render('blank');
        if ($this->_post) {
            $this->status = 'ok';
            $this->message = xg_html('YOUR_ENTRY_WAS_ADDED');
            $this->viewUrl = $this->_buildUrl('blog','show', array('id' => $this->_post->id));
            $this->viewText = xg_html('VIEW_THIS_BLOG_ENTRY');
            unset($this->_post);
        } else {
            $this->status = 'fail';
            $this->message = xg_html('CANNOT_ADD_YOUR_ENTRY');
        }
    }

    public function action_create() {
        // Used from action_createQuick()
        /* If you get here without being a member, go back to the post form */
        XG_App::enforceMembership('blog','new');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->redirectTo('new');
            return;
        }
        if (BlogArchive::instance()->my->buildStatus == BlogArchive::NOT_STARTED) {
            BlogArchive::instance()->my->buildStatus = BlogArchive::COMPLETE;
            BlogArchive::instance()->save();
        }
        $data = self::parseBlogPostFormSubmission($_POST);
        $post = BlogPost::createWith($data);

        $user = User::load($this->_user);

        // IE 6 doesn't handle <button/> elements with the same name and different values properly
        // See http://www.thescripts.com/forum/threadnav97210-2-10.html
        if ($_POST['post_action'] == 'preview') {
            $this->forwardTo('preview','blog', array('create',$post));
            return;
        } else {
            try {
                if ($_POST['featureOnMain']) {
                    XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
                    if (XG_PromotionHelper::currentUserCanPromote($post)) {
                        XG_PromotionHelper::promote($post);
                    }
                }
                XG_App::includeFileOnce('/lib/XG_TagHelper.php');
                XG_TagHelper::updateTagsAndSave($post, $_POST['tags']);
                /*  If user wants notifications for content he's created, add him to a
                 *    new follow list (otherwise it'll be created when the first comment
                 *    is added)
                 */
                if ($user->my->emailActivityPref !== 'none') {
                    //  Create the follow list, join it
                    W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_NotificationHelper.php');
                    Index_NotificationHelper::startFollowing($post);
                }
                if ($post->my->publishStatus == 'publish') {
                    $this->_widget->includeFileOnce('/lib/helpers/Profiles_BlogArchiveHelper.php');
                    Profiles_BlogArchiveHelper::addPostToArchiveIfEligible($user, $post);
                    $user->save();
                    $this->_widget->includeFileOnce('/lib/helpers/Profiles_BlogPostingHelper.php');
                    Profiles_BlogPostingHelper::logActivity($post);
                    Profiles_BlogPostingHelper::sendPings($post);
                    if (!empty($_POST['featureOnMain'])) {
                        XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
                        XG_PromotionHelper::addActivityLogItem(
                            XG_ActivityHelper::SUBCATEGORY_BLOG_POST,
                            $post
                        );
                    }
                }
                //BAZ-4324: send you to the blog detail page after post
                $this->_post = $post;
                $this->redirectTo('show','blog',array('id' => $post->id));
                return;
            } catch (Exception $e) {
                $this->forwardTo('new');
            }
        }
    }

    protected static function parseBlogPostFormSubmission($submittedData) {
        $data = array('title' => $submittedData['post_title'],
                      'description' => $submittedData['post_body'],
                      'visibility' => $submittedData['post_privacy'],
                      'mood' => $submittedData['post_mood'],
                      'addCommentPermission' => $submittedData['post_add_comment_permission'],
                      'publishStatus' => $submittedData['post_action'],
                      'tags' => $submittedData['tags']);

        if ($submittedData['post_when'] == 'now') {
            $data['publishWhen'] = 'now';
            $data['publishTime'] = gmdate('Y-m-d\TH:i:s\Z');
        } else {
            $hour = $submittedData['post_hour'];
            if ($submittedData['post_ampm'] == 'AM') {
                if ($hour == 12) { $hour = 0; }
            } else {
                if ($hour != 12) { $hour += 12; }
            }
            $publishStamp = xg_mktime($hour, $submittedData['post_min'], 0, $submittedData['post_month'], $submittedData['post_day'], $submittedData['post_year']);
            $data['publishTime'] = gmdate('Y-m-d\TH:i:s\Z', $publishStamp);
            $data['publishWhen'] = 'later';
        }
        if ($submittedData['post_action'] == 'draft') {
                // Insert default title/body if empty on draft
                foreach (array('title', 'description') as $f) {
                    if (mb_strlen(trim($data[$f])) == 0) {
                        $data[$f] = xg_text('DRAFT_BRACKETS');
                    }
                }
        } else if ($publishStamp > time()) {
            $data['publishStatus'] = 'queued';
        }
        return $data;
    }

    /**
     * Show a particular blog post.
     */
    public function action_show() {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_HtmlHelper.php');
        try {
            if (! isset($_GET['id'])) { throw new Exception('No ID specified'); }
            $cacheKeys = array("content-{$_GET['id']}");
            $filters = array('id' => $_GET['id']);
            $postInfo = BlogPost::find($filters, 0, 1);
            if ($postInfo['numPosts'] != 1) { throw new Exception("No matching post found."); }
            $this->post = $postInfo['posts'][0];
        } catch (Exception $e) {
            error_log("Can't retrieve post: " . $e->getMessage());
            $this->redirectTo('list');
            return;
        }
        if (! $this->post->my->addCommentPermission) {
            $this->post->my->addCommentPermission = BlogPost::getAddCommentPermission($this->post, User::load($this->post->contributorName));
            $this->post->save();
        }
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        XG_CommentHelper::stopFollowingIfRequested($this->post);

        // Figure out what page of comments to display; for simplicity, only
        // cache if we're displaying the first page of comments
         // How many comments on each page
         $this->pageSize = 10;
         // Pages start at 1, not 0
         $this->page = isset($_GET['page']) ? (integer) $_GET['page'] : 1;
         if ($this->page < 1) { $this->page = 1; }
         $this->start = ($this->page - 1) * $this->pageSize;
         $this->end = $this->start + $this->pageSize;
        try {
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');

            /* If the post isn't already published, make sure it's the owner looking at it */
            if (($this->post->my->publishStatus != 'publish') &&
            ($this->_user->screenName != $this->post->contributorName)) {
                throw new Exception("{$this->post->id} isn't published, won't show to: {$this->_user->screenName}");
            }
            // Only retrieve unapproved comments if the current user is the post owner
            $approved = ($this->_user->screenName != $this->post->contributorName) ? 'Y' : null;
            $this->commentInfo = Comment::getCommentsFor($this->post->id, $this->start, $this->end, $approved);

            // Prime the profiles cache: the post owner and the owners of any comments that will be displaye
            $profilesToLoad = array($this->post->contributorName);
            foreach ($this->commentInfo['comments'] as $comment) { $profilesToLoad[] = $comment->contributorName; }
            $loadedProfiles = XG_Cache::profiles($profilesToLoad);

            if ($this->_user->isLoggedIn()) {
                $this->friendStatus = Profiles_UserHelper::getFriendStatusFor($this->post->contributorName, $this->_user);
            }
            if ($this->_user->screenName == $this->post->contributorName) {
            } else {
                // If you're a friend of the current user, you get the friends archive, otherwise, you get the 'all' archive
                $isFriend = $this->_user->isLoggedIn() && ($this->friendStatus == 'friend');
            }
            XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
            $this->isPromoted = (! is_null(XG_PromotionHelper::promotedOn($this->post)));
            XG_App::includeFileOnce('/lib/XG_TagHelper.php');
            if ($this->post->my->publishStatus == 'draft') {
                $this->form = new XNC_Form(array_merge(self::formDefaults($this->post, User::load($this->post->contributorName)), array('targetAction' => 'update')));
                $this->formActionSuffix = '?id=' . urlencode($this->post->id);
            }

            $this->forwardTo('showProper', 'blog', array(array(
                    'allowComments' => Profiles_CommentHelper::canCurrentUserSeeAddCommentSection($this->post, $isFriend),
                    'commentInfo' => $this->commentInfo,
                    'form' => $this->form,
                    'formActionSuffix' => $this->formActionSuffix,
                    'isPreview' => false,
                    'pageSize' => $this->pageSize,
                    'post' => $this->post,
                    'previousPost' => BlogPost::adjacentPost('<', $this->post),
                    'nextPost' => BlogPost::adjacentPost('>', $this->post),
                    'postContributorName' => $this->post->contributorName,
                    'tags' => XG_TagHelper::getTagNamesForObject($this->post))));
        } catch (Exception $e) {
            error_log("Can't show post: " . $e->getMessage());
            $this->redirectTo('list');
        }
    }

    /**
     * Displays the detail page for a blog post.
     *
     * @param $args array
     *         allowComments - whether the current user can see the comment form
     *         commentInfo - array of comments to display, keyed by 'comments' and 'numComments'
     *         form - XNC_Form for the "This is a preview" form at the top of the page
     *         formActionSuffix - query string for the form
     *         isPreview - whether we will be displaying a preview of the post
     *         pageSize - the number of comments per page
     *         post - the BlogPost
     *         previousPost - the previous BlogPost by the same user, visible to the current user
     *         nextPost - the next BlogPost by the same user, visible to the current user
     *         postContributorName - screen name of the post author
     *         tags - the blog post's most popular tag names
     */
    public function action_showProper($args) {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_PrivacyHelper.php');
        XG_App::includeFileOnce('/lib/XG_CommentHelper.php');
        foreach ($args as $key => $value) { $this->{$key} = $value; }
        $this->hiddenVariables = self::$formVariables;
        // Set the meta description and keywords and page title (BAZ-1025)
        XG_App::includeFileOnce('/lib/XG_MetatagHelper.php');
        $this->metaDescription = $this->post->description;
        $this->metaKeywords = implode(', ', $this->tags);
        $this->userIsOwner = $this->_user->screenName == $this->postContributorName;
        $this->profile = XG_Cache::profiles($this->postContributorName);
        if (mb_strlen($this->post->title)) {
            $this->pageTitle = BlogPost::getTextTitle($this->post, ENT_QUOTES, 'UTF-8');
        } else {
            $this->pageTitle = xg_text('BLOG_POST_BY_X', xg_username($this->profile)) . ': ' . BlogPost::getTextTitle($this->post, 40);
        }
        $this->pageOwner = User::load($this->postContributorName);
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FeedHelper.php');
        $this->feedUrl = Profiles_FeedHelper::feedUrl($this->_buildUrl('blog','feed',array('user' => $this->postContributorName, 'xn_auth' => 'no')));
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        // get tags for user if they're admin/NC or contributor
        if (XG_SecurityHelper::userIsAdminOrContributor($this->_user, $this->topic)) {
            $this->currentUserTagString = XG_TagHelper::implode(XG_TagHelper::getTagNamesForObjectAndUser($this->post, $this->_user->screenName));
        }
        self::selectAppropriateTab($this->userIsOwner);
    }

    /** Names of the form elements */
    protected static $formVariables = array('tags', 'post_title','post_body','post_privacy','post_add_comment_permission','post_when','post_hour','post_ampm','post_min','post_month','post_mood','post_day','post_year','targetAction');

    public function action_preview($targetAction,$post) {
        XG_App::enforceMembership('blog','new');
        // Make sure this action is accessed only by forwarding from somewhere else.
        if (! (isset($targetAction) && isset($post))) {
            $this->redirectTo('list');
            return;
        }
        // Form variable defaults come from $_POST, which is preserved from
        // whatever action forwarded control on to preview
        $this->form = new XNC_Form(array('targetAction' => $targetAction));
        // Preserve post ID across preview form
        if (isset($_GET['id'])) {
            $this->formActionSuffix = '?id=' . $_GET['id'];
        } else {
            $this->formActionSuffix = '';
        }
        $this->forwardTo('showProper', 'blog', array(array(
                'allowComments' => false,
                'commentInfo' => array(),
                'form' => $this->form,
                'formActionSuffix' => $this->formActionSuffix,
                'isPreview' => true,
                'pageSize' => 10,
                'post' => $post,
                'postContributorName' => $this->_user->screenName,
                'tags' => array())));
    }

    public function action_previewSubmit() {
        XG_App::enforceMembership('blog','new');
        if (! isset($_POST['targetAction'])) {
            $this->redirectTo('blog','new');
            return;
        }
        if ($_POST['targetAction'] == 'create') {
            if ($_POST['post_action'] == 'edit') {
                $action = 'new';
            } else {
                $action = 'create';
            }
        }
        else {
            if ($_POST['post_action'] == 'edit') {
                $action = 'edit';
            } else {
                $action = 'update';
            }
        }
        $this->forwardTo($action);
    }

    public function action_list() {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_BlogArchiveHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_BlogListHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_UserHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FeedHelper.php');
        XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
        XG_App::includeFileOnce('/lib/XG_MetatagHelper.php');
        if ($_GET['my']) {
            XG_SecurityHelper::redirectToSignInPageIfSignedOut(XG_HttpHelper::currentUrl());
            return $this->redirectTo(XG_HttpHelper::addParameters(XG_HttpHelper::currentUrl(), array('my' => null, 'user' => XN_Profile::current()->screenName)));
        }
        $this->prepareListAction(new Profiles_BlogListHelper());
        if ($this->start > 0) { $this->nextPageUrl = XG_HttpHelper::addParameters(XG_HttpHelper::currentUrl(), array('start' => $this->start - $this->pageSize, 'pageSize' => $_GET['pageSize'])); }
        if ($this->end < $this->posts['numPosts']) { $this->previousPageUrl = XG_HttpHelper::addParameters(XG_HttpHelper::currentUrl(), array('start' => $this->end, 'pageSize' => $_GET['pageSize'])); }
        // Load all of the post owner profiles into the cache
        $profilesToLoad = array();
        foreach ($this->posts['posts'] as $post) { $profilesToLoad[$post->contributorName] = $post->contributorName; }
        $loadedProfiles = XG_Cache::profiles($profilesToLoad);
        self::selectAppropriateTab($this->userIsOwner);
        $this->render($this->templateName);
    }

    /**
     * Initializes the data for the list action.
     *
     * @param $helper Profiles_BlogListHelper  facade for services used by the list action
     */
    protected function prepareListAction($helper) {
        if (isset($_GET['user']) && isset($_GET['tag'])) {
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_BlogListModeForTagAndUser.php');
            $mode = new Profiles_BlogListModeForTagAndUser($helper, $this->_user);
        } elseif (isset($_GET['q'])) {
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_BlogListModeForSearch.php');
            $mode = new Profiles_BlogListModeForSearch($helper);
        } elseif (isset($_GET['user'])) {
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_BlogListModeForUser.php');
            $mode = new Profiles_BlogListModeForUser($helper, $this->_user);
        } elseif (isset($_GET['tag'])) {
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_BlogListModeForTag.php');
            $mode = new Profiles_BlogListModeForTag($helper);
        } elseif ($_GET['promoted']) {
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_BlogListModeForFeatured.php');
            $mode = new Profiles_BlogListModeForFeatured($helper);
        } else {
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_BlogListModeForAll.php');
            $mode = new Profiles_BlogListModeForAll($helper);
            $this->showFeaturedBlock = true;
        }
        $this->feedUrl = $mode->getFeedUrl();
        $this->user = $mode->getUser();
        $this->profile = $mode->getProfile();
        $this->tags = $mode->getTags();
        $filters = $mode->getFilters();
        // Query recent posts before setting $filters['my->publishTime']  [Jon Aquino 2008-02-04]
        $this->recentPosts = $helper->findBlogPosts($filters, 0, 7);
        $this->popularPosts = $helper->findBlogPosts(array_merge($filters, array('my.popularityCount' => array('<>', null))), 0, 7, 'my.popularityCount', 'desc');
        if ($this->showFeaturedBlock) {
            $this->featuredPosts = $helper->findBlogPosts(array_merge($filters, array('promoted' => true)), 0, 7);
        }
        if (isset($_GET['year']) && isset($_GET['month']) &&
                ($year = (integer) $_GET['year']) && ($month = (integer) $_GET['month']) &&
                ($year >= 1) && ($month >=1) && ($month <=12)) {
            $monthStartStamp = xg_mktime(0, 0, 0, $month, 1, $year);
            $nextMonth = $month + 1;
            if ($nextMonth == 13) {
                $nextMonth = 1; $nextMonthYear = $year + 1;
            } else {
                $nextMonthYear = $year;
            }
            // Beware of leap seconds! :)
            $monthEndStamp = xg_mktime(0, 0, 0, $nextMonth, 1, $nextMonthYear) - 1;
            $filters['my->publishTime'] = array(
                    array('>=', gmdate('Y-m-d\TH:i:s\Z', $monthStartStamp), XN_Attribute::DATE),
                    array('<=', gmdate('Y-m-d\TH:i:s\Z', $monthEndStamp), XN_Attribute::DATE));
            $monthNames = self::getMonths();
            $this->titleHtml = $mode->getTitleHtmlForDate($monthNames[$month], $year);
        } else {
            if ($mode->shouldHideFuturePosts()) { $filters['my->publishTime'] = array('<=', gmdate('Y-m-d\TH:i:s\Z'), XN_Attribute::DATE); } // BAZ-6271 [Jon Aquino 2008-02-27]
            $this->titleHtml = $mode->getTitleHtml();
        }
        $this->noPostsMessage = $mode->getNoPostsMessage();
        $this->noPostsMessageHasAddLink = $mode->doesNoPostsMessageHaveAddLink();
        $this->latestBlogPostsTitle = $mode->getLatestBlogPostsTitle();
        $this->mostPopularBlogPostsTitle = $mode->getMostPopularBlogPostsTitle();
        $this->monthlyArchivesTitle = $mode->getMonthlyArchivesTitle();
        $this->metaDescription = $mode->getMetaDescription();
        $this->archive = $mode->getArchive();

        $this->pageSize = $_GET['pageSize'] ? $_GET['pageSize'] : self::DEFAULT_BLOG_PAGE_SIZE; //$_GET['pageSize'] for debugging [Jon Aquino 2007-12-10]

        $this->start = max(0, $_GET['start'] ? $_GET['start'] : ($this->posts['numPosts'] - $this->pageSize));
        $this->end = $this->start + $this->pageSize;
        $this->posts = $helper->findBlogPosts($filters, $this->start, $this->end, $mode->getOrderAttribute(isset($_GET['month'])), 'desc');

        $this->pageTitle = $mode->getPageTitle();
        $this->templateName = $mode->getTemplateName($this->posts['numPosts']);
        $this->userIsOwner = $this->user && $this->user->title == $this->_user->screenName;
        $this->promoted = $_GET['promoted'];
    }

    public function action_feed() {
        // BAZ-215: In a private site, only allow feed retrieval if the URL
        // has the correct key in it
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_FeedHelper.php');
        if (! Profiles_FeedHelper::validKeyProvided()) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
        header('Content-Type: application/atom+xml');
        $this->setCaching(array('profiles-blog-feed-' . md5(XG_HttpHelper::currentUrl())), 1800);
        if ($_GET['test_caching']) { var_dump('Not cached'); }
        try {
            $filters = array();
            if (isset($_GET['user'])) {
                $filters['contributorName'] = $_GET['user'];
                $this->user = User::load($_GET['user']);
                $this->profile = XN_Profile::load($_GET['user']);
                $this->title = xg_text('XS_POSTS', ucfirst(xg_username($this->profile)));
                $this->_widget->includeFileOnce('/lib/helpers/Profiles_CacheHelper.php');
            } elseif (isset($_GET['promoted']) && ($_GET['promoted'] == 1)) {
                $this->title = xg_text('FEATURED_BLOG_POSTS');
                $filters['promoted'] = true;
            } elseif (isset($_GET['tag'])) {
                $this->title = xg_text('ALL_BLOG_POSTS_TAGGED_X',$_GET['tag']);
                $filters['tag->value'] = array('eic', $_GET['tag']);
            } else {
                $this->title = xg_text('EVERYONES_POSTS');
            }
            // Bypass the default visibility filtering and just retrieve
            // posts that are visible to everyone
            $filters['ignoreVisibility'] = true;
            $filters['my->visibility'] = 'all';
            $filters['my->publishTime'] = array('<=', gmdate('Y-m-d\TH:i:s\Z'), XN_Attribute::DATE);
            $filters['my->publishStatus'] = 'publish';
        } catch (Exception $e) {
            error_log("Feed error: " . $e->getMessage());
            $this->redirectTo('list');
            return;
        }
        try {
            $this->postInfo = BlogPost::find($filters, 0, 30);
            // If it's a feed of everyone's posts, load the profile objects from the cache
            if (! $this->profile) {
                $profiles = array();
                foreach ($this->postInfo['posts'] as $post) { $profiles[$post->contributorName] = $post->contributorName; }
                $this->profiles = XG_Cache::profiles($profiles);
            }
        } catch (Exception $e) {
            error_log("Feed error: " . $e->getMessage());
            $this->redirectTo('list');
            return;
        }
    }

    /**
     * Manage posts
     */
    public function action_managePosts() {
        XG_App::enforceMembership('blog','managePosts');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->forwardTo('managePostsDelete','blog');
            return;
        }

        $this->pageSize = 10;
        // Pages start at 1, not 0
        $this->page = isset($_GET['page']) ? (integer) $_GET['page'] : 1;
        if ($this->page < 1) { $this->page = 1; }

        $this->start = ($this->page - 1) * $this->pageSize;
        $this->end = $this->start + $this->pageSize;
        // Draft posts must be at the top of the list. So first, we query for draft posts,
        // using start and end. If, after that, we have less than $pageSize posts, we query
        // for non-draft posts, ordered by publishTime to fill up the page (using start and
        // end as well, to enable paging through non-drafts
        $draftInfo = BlogPost::find(array('contributorName' => $this->_user->screenName, 'my->publishStatus' => 'draft'),
                                    $this->start, $this->end);
        // Get some regular posts
        if (count($draftInfo['posts']) < $this->pageSize) {
            // How many regular posts to retrieve?
            $regularPostsToRetrieve = $this->pageSize - count($draftInfo['posts']);
            // Where to start in the regular posts query?
            // Are we on a hybrid page?
            if ($regularPostsToRetrieve < $this->pageSize) {
                $regularPostStart = 0;
            }
            // or a page of all regular posts?
            else {
                $regularPostStart = $this->start - $draftInfo['numPosts'];
            }
            $postInfo = BlogPost::find(array('contributorName' => $this->_user->screenName,
                                             'my->publishStatus' => array('!=', 'draft')),
                                             $regularPostStart,
                                             $regularPostStart + $regularPostsToRetrieve,
                                             'my->publishTime','desc');
            /* If we didn't find any posts, it might be because we just deleted some posts on the last
             * page and now we have to jump back a page (BAZ-201) */
            if (($regularPostStart >= $postInfo['numPosts']) && ($postInfo['numPosts'] > 0)) {
                $lastPage = (int)(($postInfo['numPosts'] - 1) / $this->pageSize) + 1;
                if ($lastPage < 1) { $lastPage = 1; }

                $regularPostStart = (($lastPage - 1) * $this->pageSize) - $draftInfo['numPosts'];
                if ($regularPostStart < 0) { $regularPostStart = 0; }
                $postInfo = BlogPost::find(array('contributorName' => $this->_user->screenName,
                                                 'my->publishStatus' => array('!=', 'draft')),
                                                 $regularPostStart,
                                                 $regularPostStart + $regularPostsToRetrieve,
                                                 'my->publishTime','desc');
                $this->page = $lastPage;
            }

            $this->posts = array_merge($draftInfo['posts'], $postInfo['posts']);
        }
        // The whole page is just draft posts, but we need to do a query to find the
        // count of non-draft posts
        else {
            $this->posts = $draftInfo['posts'];
            $postInfo = BlogPost::find(array('contributorName' => $this->_user->screenName,
                                             'my->publishStatus' => array('!=', 'draft')),
                                             0, 1);
        }
        $this->numPages = ceil(($draftInfo['numPosts'] + $postInfo['numPosts']) / $this->pageSize);
        self::selectAppropriateTab(true);
    }

    public function action_managePostsDelete() {
        XG_App::enforceMembership('blog','managePosts');
        // For each of the posts for which deletion is requested, delete it (and all attached
        // comments) if the current user is allowed to delete the post
        if (isset($_POST['id']) && is_array($_POST['id'])) {
            $user = User::load($this->_user);
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_BlogArchiveHelper.php');
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_CacheHelper.php');
            $cacheKeys = array();
            foreach ($_POST['id'] as $id) {
                $post = BlogPost::findById('BlogPost',$id);
                if ($post && XG_SecurityHelper::userIsContributor($this->_user, $post)) {
                    Profiles_BlogArchiveHelper::removePostFromArchiveIfEligible($user, $post);
                    BlogPost::remove($post);
                }
            }
            $user->save();
        }
        $this->redirectTo('managePosts','blog',array('page' => isset($_GET['page']) ? $_GET['page'] : 1));
    }

    public function action_manageComments() {
      XG_App::enforceMembership('blog','manageComments');
      // When to truncate comments
      $this->truncateCommentAt = 50;

      $this->pageSize = 20;
      // Pages start at 1, not 0
      $this->page = isset($_GET['page']) ? (integer) $_GET['page'] : 1;
      if ($this->page < 1) { $this->page = 1; }

      $this->start = ($this->page - 1) * $this->pageSize;
      $this->end = $this->start + $this->pageSize;

      $commentInfo = Comment::getCommentsForContentBy($this->_user, $this->start, $this->end,null,'createdDate','desc',array('my->attachedToType' => 'BlogPost'));
      //  BAZ-3288 - Reset comments to approve count if it's negative
      // this also keeps counts synchronized preventing BAZ-10147 [ywh 2008-09-24]
      $user = User::load($this->_user->screenName);
      if ($user->my->commentsToApprove != $commentInfo['numComments']) {
          $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
          Profiles_CommentHelper::updateCommentsToApprove($user);
          $user->save();
      }
      /* If we didn't find any comments, it might be because we just deleted some comments on the last
       * page and now we have to jump back a page (BAZ-201) */
      if (($this->start >= $commentInfo['numComments']) && ($commentInfo['numComments'] > 0)) {
          $lastPage = (int)(($commentInfo['numComments'] - 1)/ $this->pageSize);
          if ($lastPage < 1) { $lastPage = 1; }
          $this->start = ($lastPage - 1) * $this->pageSize;
      $commentInfo = Comment::getCommentsForContentBy($this->_user, $this->start, $this->end,null,'createdDate','desc',array('my->attachedToType' => 'BlogPost'));
          $this->page = $lastPage;
      }

      $this->numPages = ceil($commentInfo['numComments'] / $this->pageSize);
      $this->comments = $commentInfo['comments'];

      // Load all of the profiles of comment owners in one query
      $this->commentContributors = array();
      $this->posts = array();
      foreach ($this->comments as $comment) {
          $this->commentContributors[$comment->contributorName] = true;
          $this->posts[$comment->my->attachedTo] = true;
      }
      foreach (XG_Cache::profiles(array_keys($this->commentContributors)) as $profile) {
          $this->commentContributors[$profile->screenName] = $profile;
      }

      // Load all of the posts that the comments refer to
      foreach (XG_Cache::content(array_keys($this->posts)) as $post) {
          $this->posts[$post->id] = $post;
      }
      self::selectAppropriateTab(true);

    }

    public function action_manageCommentsSubmit() {
        XG_App::enforceMembership('blog','manageComments');

        // For each of the comments for which action is required, delete or approve it
        // if the current user is the owner of the post that the comment is attached to
        // Deletion/Approval by others is handled in other places
        if (isset($_POST['comment_action']) && isset($_POST['id']) && is_array($_POST['id'])) {
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_CacheHelper.php');
            $cacheKeys = array();
            $user = User::load($this->_user->screenName);
            foreach ($_POST['id'] as $commentId) {
                $comment = Comment::load($commentId);
                if ($comment && ($comment->my->attachedToAuthor == $this->_user->screenName)) {
                    if ($_POST['comment_action'] == 'approve') {
                        if ($comment->my->approved == 'N') {
                            Comment::approve($comment);
                            XG_App::includeFileOnce('/lib/XG_ActivityHelper.php');
                            $this->attachedToContent = self::getAttachedTo($comment->my->attachedToType, $comment->my->attachedTo);
                            if ($this->attachedToContent->my->visibility == 'all') {
                                XG_ActivityHelper::logActivityIfEnabled(XG_ActivityHelper::CATEGORY_NEW_COMMENT, XG_ActivityHelper::SUBCATEGORY_BLOG, $comment->contributorName, array($comment,$this->attachedToContent));
                            }
                        }
                    } elseif($_POST['comment_action'] == 'delete') {
                        Comment::remove($comment);
                    }
                }
            }
            $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
            Profiles_CommentHelper::updateCommentsToApprove($user);
            $user->save();
        }

        $this->redirectTo('manageComments','blog',array('page' => isset($_GET['page']) ? $_GET['page'] : 1));
    }

    /** For when you need some months. Don't we all, really? */
    /* @todo: this really belongs in a more general lang/constants place */

    public static function getMonths() {
        return array(1 => xg_text('JANUARY'), xg_text('FEBRUARY'), xg_text('MARCH'), xg_text('APRIL'), xg_text('MAY'), xg_text('JUNE'), xg_text('JULY'), xg_text('AUGUST'), xg_text('SEPTEMBER'), xg_text('OCTOBER'), xg_text('NOVEMBER'), xg_text('DECEMBER'));
    }

    public function action_publishInFuture($maxToPublish = 25, $fromBot = false) {
        if (! $fromBot) { return; }
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_BlogPostingHelper.php');
        /* Don't worry about looping if there are > 100 queued posts; they'll get caught
         * by the next bot run */
        Profiles_BlogPostingHelper::task_publishPosts($maxToPublish, true);
    }

    /**
     * Does nothing. Used to cancel an IFrame-based upload.
     * Set xn_out to json.
     */
    public function action_noOp() {
        // Output something just in case, to prevent possible 400 errors [Jon Aquino 2007-04-13]
        $this->x = 1;
        // @todo Remove the above line (and retest cancelling an upload), as xn_out=json always outputs something [Jon Aquino 2007-04-25]
    }

    public function action_upload() {
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        /* If there is a valid file, save it to the content store */
        if (isset($_POST['file']) && isset($_POST['file:status']) && ($_POST['file:status'] == 0)) {
            $isImage = (isset($_POST['image']) && ($_POST['image'] == 1));
            $this->isImage = (integer) $isImage;
            try {
                // Force uploaded stuff to be private in case the post isn't viewable by everyone
                list($file, $filename) = XG_FileHelper::createUploadedFileObject('file', true);
                $id = $file->id;
                $fileUrl = $file->fileUrl('data');
                if ($isImage) {
                    $wrap = (isset($_POST['wrap']) && ($_POST['wrap'] == 'yes'));
                    $attrs = 'alt=""';

                    /* BAZ-1694: compute appropriate image dimensions */
                    list ($imageWidth, $imageHeight) = $file->imageDimensions('data');

                    // Thumbnail
                    if (isset($_POST['thumb']) && ($_POST['thumb'] == 'yes') &&
                    isset($_POST['size']) && ($size = (integer) $_POST['size'])) {
                        XG_App::includeFileOnce('/lib/XG_ImageHelper.php');
                        $imgSrc = XG_HttpHelper::addParameter($fileUrl, 'width', $size);
                        if ($imageWidth && $imageHeight) {
                            list($finalImageWidth, $finalImageHeight) = XG_ImageHelper::getDimensionsScaled($imageWidth, $imageHeight, $size, $size);
                            $imgSrc = XG_HttpHelper::addParameter($fileUrl, 'width', $finalImageWidth);
                        }
                    } else {
                        // We're not scaling the image, so the final dims are the original ones
                        $imgSrc = $fileUrl;
                        $finalImageWidth = $imageWidth;
                        $finalImageHeight = $imageHeight;
                    }
                    if ($finalImageWidth && $finalImageHeight) {
                        $attrs .= ' width="' . $finalImageWidth . '" height="' . $finalImageHeight . '"';
                    }
                    // Image alignment
                    $aligned = false;
                    if (isset($_POST['align']) &&
                    (($_POST['align'] == 'left') || ($_POST['align'] == 'right'))) {
                        $aligned = true;
                        if ($wrap) {
                            $attrs .= ' style="float: '.$_POST['align'].';"';
                        } else {
                            $prefix = '<p style="text-align: ' . $_POST['align'] . ';">';
                            $suffix = '</p>';
                        }
                    } else if (! $wrap) {
                        $prefix = '<p>';
                        $suffix = '</p>';
                    }

                    $html = '<img src="'. xnhtmlentities($imgSrc).'" ' . $attrs . '/>';

                    // Popup
                    if (isset($_POST['popup']) && ($_POST['popup'] == 'yes')) {
                        $html = $prefix . '<a class="noborder" href="'.xnhtmlentities($fileUrl).'" target="_blank">' . $html . '</a>' . $suffix;
                    } else {
                        $html = $prefix . $html . $suffix;
                    }

                    $this->html = $html;
                } else {
                    $this->html = '<a href="'.xnhtmlentities($fileUrl).'">'.xnhtmlentities($filename).'</a>';
                }
                // Encode to work around IE's broken JSON parsing
                $this->html = rawurlencode($this->html);

                if ($_GET['xn_out'] == 'json') {
                    // Workaround for BAZ-1366
                    $_REQUEST['dojo_transport'] = '';
                }

            } catch (Exception $e) {
                error_log('File upload error: ' . $e->getMessage());
                $this->error = xg_text('UPLOAD_DID_NOT_WORK');
            }
        } else {
            $this->error = xg_text('UPLOAD_DID_NOT_WORK');
        }
    }

    private static function getAttachedTo($attachedToType, $attachedTo) {
        if ($attachedToType == 'BlogPost') {
           $postInfo = BlogPost::find(array('id' => $attachedTo));
           if (count($postInfo['posts'][0]) != 1) {
               throw new Exception("Couldn't find the post to attach the comment to");
           }
           return $postInfo['posts'][0];
        } else if ($attachedToType == 'User') {
           $user = User::load($attachedTo);
           if (! $user) {
               throw new Exception("Couldn't find the user to attach the comment to");
           }
           return $user;
        }
    }

    /**
     * Sets the tags for the given object for the current user.
     *
     * Expected GET parameters:
     *     xn_out  Should always be "json"
     */
    public function action_tag() {
        $this->_widget->includeFileOnce('/lib/helpers/Profiles_HtmlHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (1253843659)'); }
        XG_HttpHelper::trimGetAndPostValues();
        $postInfo = BlogPost::find(array('id' => $_GET['id']), 0, 1);
        if ($postInfo['numPosts'] != 1) { throw new Exception("No matching post found (1721141976)."); }
        $post = $postInfo['posts'][0];
        // only the contributor can tag (BAZ-7607) [ywh 2008-05-14]
        if (! XG_SecurityHelper::userIsAdminOrContributor($this->_user, $post)) { throw new Exception('Not allowed (1893727826)'); }
        XG_App::includeFileOnce('/lib/XG_TagHelper.php');
        XG_TagHelper::updateTagsAndSave($post, $_POST['tags']);
        $this->html = Profiles_HtmlHelper::tagHtmlForDetailPage(XG_TagHelper::getTagNamesForObject($post));
    }

    /**
     * Increments the view count for the specified blog post.
     *
     * Expected GET parameters:
     *     - xn_out - should always be "json"
     *     - id - content ID of the BlogPost
     */
    public function action_incrementViewCount() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST (1065400137)'); }
        $postInfo = BlogPost::find(array('id' => $_GET['id']), 0, 1);
        if ($postInfo['numPosts'] != 1) { throw new Exception('No matching post found for ' . $_GET['id']); }
        $post = $postInfo['posts'][0];
        XG_App::includeFileOnce('/lib/XG_PageViewHelper.php');
        XG_PageViewHelper::incrementViewCount($post);
    }

    /**
     * Selects the appropriate tab to display for Blog actions, based on whether a Blog tab exists and whether the user is the blog owner
     *
     * @param $assumeOwner boolean; whether the current function is one where we assume the user is the blog owner
     */
    private function selectAppropriateTab($userIsOwner) {
        XG_App::includeFileOnce('/lib/XG_TabLayout.php');
        if (XG_TabLayout::isEnabled()) { // returns true unless explicitly disabled
            $tabLayout = XG_TabLayout::loadOrCreate(false);
            if ($tabLayout) {
                $tabEnabled = $tabLayout->hasTab('blogs') &&
                                $tabLayout->getTab('blogs')->isVisibleToUser(XN_Profile::current());
            }
        } else {
            $tabEnabled = W_Cache::getWidget('profiles')->config['showBlogsTab'];
        }
        if ($tabEnabled) {
            $this->tab = 'blogs';
        } else {
            $this->tab = $userIsOwner ? 'profile' : 'members';
        }
    }

}
