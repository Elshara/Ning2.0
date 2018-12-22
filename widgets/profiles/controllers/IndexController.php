<?php

class Profiles_IndexController extends XG_BrowserAwareController {
    // The index action gets called from the top-level nav, so we should
    // send people off to their profile page from here
    public function action_index() {
        XG_App::enforceMembership('index','index');
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/profile/' . User::profileAddress($this->_user->screenName);
        header("Location: $url");
        exit;
    }
    
    public function action_index_iphone() {
        XG_App::enforceMembership('index','index');
        $url = xg_absolute_url('/profile/' . User::profileAddress($this->_user->screenName));
        header("Location: $url");
        exit;
    }

    /** Supply approval links to the right-hand sidebar */
    public function action_approvalLink() {
        $this->chattersToApprove = 0;
        $this->commentsToApprove = 0;

        if (!$this->_user->isLoggedIn()) {
            return;
        }
        $user = User::load($this->_user);
        if (!$user) {
            return;
        }

        //  Moderation counts are now stored on the User object (BAZ-2970)
        //  If they're not there, initialize them (migrate)
        if (is_null($user->my->chattersToApprove)) {
            $counts = Comment::getCounts($user);
            $user->my->chattersToApprove = intval($counts['commentToApproveCount']);
            $saveUser = TRUE;
        }

        if (is_null($user->my->commentsToApprove)) {
            $commentInfo = Comment::getCommentsForContentBy($this->_user, 0, 1,
                    'N', 'createdDate','desc', $filters);
            //  BAZ-1017: Chatters are double counted since they're comments on
            //    the User object - subtract the count
            $user->my->commentsToApprove = intval($commentInfo['numComments'])
                    - $user->my->chattersToApprove;
            $saveUser = TRUE;
        }

        if ($saveUser) {
            $user->save();
        }

        $this->chattersToApprove = $user->my->chattersToApprove;
        $this->commentsToApprove = $user->my->commentsToApprove;
        
        /* Provide the number of members waiting for approval for display to
        admins (BAZ-4543) */
        if (XG_SecurityHelper::userIsAdmin() && XG_App::membersAreModerated()) {
            $filters = array('pending' => true);
            $userInfo = User::find($filters, 0, 1, null, null, true /* caching */);
            $this->usersToApprove = $userInfo['numUsers'];
        }
        else {
            $this->usersToApprove = null;
        }
    }

    /**
     * The SearchController in the main widget is interested to know what this
     * widget has to say about app-wide search queries. @see BAZ-3821
     *
     * @param $query XN_Query The query object to modify
     */
    public function action_annotateSearchQuery($query) {
        /* Exclude BlogPosts that don't have my.publishStatus == 'publish' */
        $query->filter(XN_Filter::any(XN_Filter('type','!like','BlogPost'),
                                      XN_Filter::all(XN_Filter('type','like','BlogPost'),
                                                     XN_Filter('my.publishStatus','like','publish'))));
    }


    /** Handle incoming detail links */
    public function action_detail($content = null) {
        // If content is supplied because this action is dispatched from the main
        // /xn/detail handler, then use that. Otherwise, redirect to the homepage.
        // Other mozzles may want to get an ID from the query string (or elsewhere)
        // if they want to support direct access to the index/detail detail view redirector
        if (is_null($content)) {
            header("Location: http://{$_SERVER['HTTP_HOST']}/");
            exit();
        }
        switch ($content->type) {
            case 'BlogPost':
                $this->redirectTo('show','blog',array('id' => $content->id));
                break;
            case 'Comment':
                $this->_widget->includeFileOnce('/lib/helpers/Profiles_CommentHelper.php');
                header('Location: ' . Profiles_CommentHelper::url($content));
                break;
            case 'User':
                $profileAddress = User::profileAddress($content->contributorName);
                $url = "http://{$_SERVER['HTTP_HOST']}/profile/{$profileAddress}";
                header("Location: $url");
                exit();
                break;
            default:
                header("Location: http://{$_SERVER['HTTP_HOST']}/");
                exit();
        }
    }

    /**
     * Supply an 'add content' form for the site and user setup.
     * If the request method is POST, attempt to save any provided info
     * If the request method is GET, just render the template. No error
     * messages are returned to speed the setup/join process
     */
    public function action_addContent() {
        $this->prefix = W_Cache::current('W_Widget')->dir;
        try {
            if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST[$this->prefix]) && is_array($_POST[$this->prefix])) {
                $input = $_POST[$this->prefix];
                $subject = isset($input['subject']) ? trim($input['subject']) : null;
                $entry   = isset($input['entry']) ? trim($input['entry']) : null;
                if (mb_strlen($subject) && mb_strlen($entry)) {
                    $data = array('title' => $subject, 'description' => xg_nl2br($entry));
                    $post = BlogPost::createWith($data);
                    $post->save();
                }
            }
        } catch (Exception $e) {
            error_log("$this->prefix addContent error: {$e->getMessage()}");
            if (is_callable(array($e,'getErrorsAsString'))) {
                error_log($e->getErrorsAsString());
            }
        }
    }


}