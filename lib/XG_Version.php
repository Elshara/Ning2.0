<?php

/**
 * This class tracks the code revisions in the app and makes possible
 * taking actions when code in the app or (eventually) in individual
 * widgets has changed
 *
 * @ingroup XG
 */
class XG_Version {
    /**
     * The full path of the code which we hope will contain some parseable
     *   branch information
     */
    protected static $release = '$HeadURL: svn://app.svn.ninginc.com/bazel/tags/3.7.1/lib/XG_Version.php $';

    /**
     * The latest revision of the app code. This should be updated on every submit
     */
    protected static $revision = '$Revision: 9983 $'; /*1223632136*/

    /**
     * The appropriate string value for the latest app revision
     *
     * @return string
     */
    public static function currentCodeVersion() {
        if (preg_match('@/branches/([0-9][0-9\.]+)/lib/XG_Version.php@u', self::$release, $matches)) {
            $release = $matches[1] . ':';
        } elseif (preg_match('@/tags/([0-9][0-9\.]+)/lib/XG_Version.php@u', self::$release, $matches)) {
            $release = $matches[1] . ':';
        } else {
            $release = '';
        }
        $revision = intval(mb_substr(self::$revision, 2+mb_strpos(self::$revision, ':')));
        return $release . $revision;
    }

    /**
     *  Is the app-wide code version newer than the specified version?
     *
     *  Used in XG_App to detect code updates
     */
    protected static function codeIsNewerThan($haveString) {
        return self::olderThan($haveString, self::currentCodeVersion());
    }

    /**
     * Compare two version strings of the form release:revision first by release,
     *   then by revision
     *  Returns < 0 if $stringOne is less than $stringTwo; > 0 if $stringOne is greater than $stringTwo,
     *       and 0 if they are equal.
     */
    protected static function compareByReleaseAndRevision($stringOne, $stringTwo) {
        // TODO: Someday/maybe use the standard convention of
        // returning < 0 if $stringTwo is less than $stringOne [Jon Aquino 2007-11-28]
        if (mb_strpos($stringOne, ':') !== FALSE) {
            list($releaseOne, $revisionOne) = explode(':', $stringOne);
        } elseif (mb_strpos($stringOne, '.') !== FALSE) {
            $releaseOne = $stringOne;
            $revisionOne = '';
        } else {
            $releaseOne = '';
            $revisionOne = $stringOne;
        }

        if (mb_strpos($stringTwo, ':') !== FALSE) {
            list($releaseTwo, $revisionTwo) = explode(':', $stringTwo);
        } elseif (mb_strpos($stringTwo, '.') !== FALSE) {
            $releaseTwo = $stringTwo;
            $revisionTwo = '';
        } else {
            $releaseTwo = '';
            $revisionTwo = $stringTwo;
        }

        // '' release indicates trunk (BAZ-5387) [Jon Aquino 2007-11-27]
        if (($releaseOne === '' && $releaseTwo !== '') || ($releaseOne !== '' && $releaseTwo === '')) {
            return self::compareByParts($revisionOne, $revisionTwo);
        }

        //  First, compare releases
        if (($comp = self::compareByParts($releaseOne, $releaseTwo)) !== 0) {
            return $comp;
        }

        //  Compare revisions only if releases match
        return self::compareByParts($revisionOne, $revisionTwo);
    }

    /**
     *  Compare two version number strings in a revision number aware manner,
     *    i.e. 1.5.10 > 1.5.2 > 1.4.16 > 506 > 247
     *  Returns < 0 if $v1 is less than $v2; > 0 if $v1 is greater than $v2,
     *       and 0 if they are equal.
     */
    protected static function compareByParts($v1, $v2) {
        $v1Parts = explode('.', $v1);
        $v2Parts = explode('.', $v2);

        // First, compare elements one by one until one list runs out
        for ($n = 0; $n < count($v1Parts) && $n < count($v2Parts); $n++) {
            if ($v1Parts[$n] != $v2Parts[$n]) {
                return $v1Parts[$n] > $v2Parts[$n] ? 1 : -1;
            }
        }

        // If initial elements match, the longer list is greater
        if (count($v1Parts) == count($v2Parts)) {
            return 0;
        } else {
            return count($v1Parts) > count($v2Parts) ? 1 : -1;
        }
    }

    /**
     * Takes action when the app code is upgraded.
     */
    public static function noticeNewCodeVersion() {
        self::noticeNewCodeVersionProper(W_Cache::getWidget('main'), 'XG_Version');
    }

    /**
     * Takes action when the app code is upgraded. Does nothing if the app
     * has just been created - see XG_App::initializeConfiguration().
     *
     * @param $mainWidget W_Widget  the main mozzle
     * @param $versionClassName string|object  "XG_Version", or a unit-testing object
     */
    protected static function noticeNewCodeVersionProper($mainWidget, $versionClassName) {
        // Check appSubdomain, as we shouldn't run appWideCodeUpgrade if the network has
        // just been created. [Jon Aquino 2008-03-11]
        // TODO: Move the appSubdomain check to loadWidgets() (BAZ-6591) [Jon Aquino 2008-03-11]
        if (mb_strtolower($mainWidget->config['appSubdomain']) !== mb_strtolower(XN_Application::load()->relativeUrl)) { return; }
        // Use call_user_func to make this code unit-testable [Jon Aquino 2008-02-23]
        $currentCodeVersion = call_user_func(array($versionClassName, 'currentCodeVersion'));
        $currentAppVersion = $mainWidget->config['appCodeVersion'];
        // Lock for 600 seconds to make certain that appWideCodeUpgrade does not run multiple times (BAZ-7304) [Jon Aquino 2008-04-17]
        if (call_user_func(array($versionClassName, 'codeIsNewerThan'), $currentAppVersion) && XG_Cache::lock('appWideCodeUpgrade', 600)) {
            $mainWidget->config['appCodeVersion'] = $currentCodeVersion;
            $mainWidget->saveConfig();
            call_user_func(array($versionClassName, 'appWideCodeUpgrade'), $currentCodeVersion, $currentAppVersion);
        }
    }

    /**
     * Called after the app has been upgraded. This is not called when an app has
     * been created - see XG_App::initializeConfiguration().
     *
     * CAUTION: This code runs on the very first request that comes in after the
     * centralized code has been updated, so it could be as the app owner, any
     * random user, or no signed in user. This means that saving new objects to
     * the content store is a bad idea in this method
     *
     * @param $currentCodeVersion string The current running version of the code
     * @param $currentAppVersion string The last version of the code the app knows about
     *
     */
    protected static function appWideCodeUpgrade($currentCodeVersion, $currentAppVersion) {
        error_log('appWideCodeUpgrade start');
        error_log("currentCodeVersion: $currentCodeVersion, currentAppVersion: $currentAppVersion");
        XG_App::includeFileOnce('/lib/XG_JobHelper.php');
        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');

        try {
            self::logSectionStart('Invalidate the local cache');
            XG_Cache::invalidate(XG_Cache::INVALIDATE_ALL);
            /* BAZ-4932: XN_Cache is not invalidated here */
            self::logSectionEnd();
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            self::logSectionStart('moveCssFiles');
            //  Move CSS to xn_resources if necessary (BAZ-2109)
            self::moveCssFiles();
            self::logSectionEnd();
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            self::logSectionStart('createNewInstance(\'events\')');
            self::createNewInstance('events');
            self::logSectionEnd();
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            self::logSectionStart('createNewInstance(\'groups\')');
            //  For 1.6: Create an instance of the groups widget if there isn't
            //    one already (BAZ-2781)
            self::createNewInstance('groups');
            self::logSectionEnd();
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            self::logSectionStart('createNewInstance(\'music\')');
            //  For 1.7: Create an instance of the music widget if there isn't
            //    one already (BAZ-2933)
            self::createNewInstance('music', 'music', '<addMusicPermission>all</addMusicPermission>');
            self::logSectionEnd();
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            self::logSectionStart('createBadgeAndPlayerConfig');
            //  If we don't have any 1.8-style badge and widget config yet, generate it
            self::createBadgeAndPlayerConfig();
            self::logSectionEnd();
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            if (self::olderThan($currentAppVersion, '1.8')) {
                self::logSectionStart('generateResources');
                //  Create the badge-config.xml file and the avatar-grid image [Jon Aquino 2007-06-12]
                XG_EmbeddableHelper::generateResources();
                self::logSectionEnd();
            }
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            if (self::olderThan($currentAppVersion, '1.10')) {
                self::logSectionStart('setStandardIndexingForAllModels');
                /* For 1.10: Ensure the different content types have appropriate searchability */
                XG_App::includeFileOnce('/lib/XG_ShapeHelper.php');
                XG_ShapeHelper::setStandardIndexingForAllModels();
                self::logSectionEnd();
            }
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            if (self::olderThan($currentAppVersion, '2.0')) {
                self::logSectionStart('User shape search tweaks');
                /* For 1.10.1: Add indexing for User.my.xg_index_status (BAZ-4024) */
                /* For 2.0: Add indexing for User.my.profileAddress */
                $userShape = XN_Shape::load('User');
                $callSave = false;
                if (isset($userShape->attributes['my.xg_index_status']) && ($userShape->attributes['my.xg_index_status']->indexing != 'phrase')) {
                    $userShape->setAttribute('my.xg_index_status', array('indexing' => 'phrase'));
                    $callSave = true;
                }
                if (isset($userShape->attributes['my.profileAddress']) && ($userShape->attributes['my.profileAddress']->indexing != 'phrase')) {
                    $userShape->setAttribute('my.profileAddress', array('indexing' => 'phrase'));
                    $callSave = true;
                }
                // TODO: Use setShapeAttributes() [Jon Aquino 2008-03-19]
                if ($callSave) {
                    $userShape->save();
                }
                self::logSectionEnd();
            }
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            if (self::olderThan($currentAppVersion, '3.0')) {
                self::logSectionStart('setShapeAttributes');
                self::setShapeAttributes(array(
                    'Photo' => array('my.location' => array('type' => XN_Attribute::STRING, 'indexing' => 'phrase')),
                    'Video' => array('my.location' => array('type' => XN_Attribute::STRING, 'indexing' => 'phrase')),
                    'Group' => array('my.location' => array('type' => XN_Attribute::STRING, 'indexing' => 'phrase')),
                    'User' => array('my.fullName' => array('type' => XN_Attribute::STRING, 'indexing' => 'text')),
                ));
                self::logSectionEnd();
            }
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            self::logSectionStart('createNewInstance(\'activity\')');
            //  For 1.11: Create an instance of the activity widget if there isn't
            //    one already (BAZ-4189)
            self::createNewInstance('activity','activity','<logNewContent type="string">Y</logNewContent>
            <logNewComments type="string">Y</logNewComments>
            <logNewMembers type="string">Y</logNewMembers>
            <logProfileUpdates type="string">Y</logProfileUpdates>', 1, 0, 1);
            self::logSectionEnd();
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            if (self::olderThan($currentAppVersion, '2.0')) {
                self::logSectionStart('migratePrivacySettings');
                /* For 2.0: migrate privacy settings (BAZ-4413) */
                self::migratePrivacySettings();
                self::logSectionEnd();
            }
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            if (self::olderThan($currentAppVersion, '2.0')) {
                self::logSectionStart('bazel_1.11.1_to_2.0');
                /* For 2.0: populate XN_ProfileSet::USERS alias (BAZ-4566, BAZ-4595) */
                try {
                    XN_REST::post('/xn/rest/migration/bazel_1.11.1_to_2.0');
                } catch (Exception $e) {
                    error_log("Members alias migration failed: {$e->getMessage()}");
                }
                self::logSectionEnd();
            }
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            self::logSectionStart('createCrossDomainXml');
            /* For 2.2 Create a crossdomain.xml file if there isn't */
            self::createCrossDomainXml();
            self::logSectionEnd();
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            self::logSectionStart('playerDisplayOpacity');
            /* For 2.3 Add the playerDisplayOpacity parameter on the music config xml
            (there is no UI to change this value, but it is important to have it as an xml attribute to allow NC manual tweaking, see BAZ-5789) */
            $musicWidget = W_Cache::getWidget('music');
            if (!isset($musicWidget->privateConfig['playerDisplayOpacity'])) {
                $musicWidget->privateConfig['playerDisplayOpacity'] = 50;
                $musicWidget->saveConfig();
            }
            self::logSectionEnd();
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            self::logSectionStart('playerSmoothing');
            /* For 2.3 Add new parameters on the video config xml */
            $videoWidget = W_Cache::getWidget('video');
            $hasNewAttributes = false;
            if (!isset($videoWidget->privateConfig['playerSmoothing'])) { $videoWidget->privateConfig['playerSmoothing'] = 'Y'; $hasNewAttributes = true;}
            if (!isset($videoWidget->privateConfig['playerHorizontalResize'])) { $videoWidget->privateConfig['playerHorizontalResize'] = 'N';  $hasNewAttributes = true;}
            if (!isset($videoWidget->privateConfig['playerVerticalResize'])) { $videoWidget->privateConfig['playerVerticalResize'] = 'Y';  $hasNewAttributes = true;}
            if (!isset($videoWidget->privateConfig['videoMaxWidth'])) { $videoWidget->privateConfig['videoMaxWidth'] = XG_EmbeddableHelper::VIDEO_WIDTH; $hasNewAttributes = true; }
            if (!isset($videoWidget->privateConfig['videoMaxHeight'])) { $videoWidget->privateConfig['videoMaxHeight'] = XG_EmbeddableHelper::VIDEO_HEIGHT; $hasNewAttributes = true; }
            if ($hasNewAttributes) $videoWidget->saveConfig();
            self::logSectionEnd();
        } catch (Exception $e) {
            self::logException($e);
        }

        if (self::olderThan($currentAppVersion, '3.0')) {
            try {
                self::logSectionStart('upgradeToNewGrid');
                self::upgradeToNewGrid();
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
        }

        if (self::olderThan($currentAppVersion, '3.1')) {
            try {
                self::logSectionStart('Notes_UrlHelper::reroute');
                self::createNewInstance('notes','notes','<router>Notes_UrlHelper::reroute</router>');
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
        }

        if (self::olderThan($currentAppVersion, '3.2')) {
            try {
                self::logSectionStart('csrfSalts');
                // Clear the CSRF salts, to initiate the 24-hour grace period
                // allowing async jobs and video callbacks without the CSRF token to finish. [Jon Aquino 2008-04-28]
                W_Cache::getWidget('main')->privateConfig['csrfSalts'] = null;
                W_Cache::getWidget('main')->saveConfig();
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
        }

        if (self::olderThan($currentAppVersion, '3.2')) {
            try {
                self::logSectionStart('addWelcomeBoxIfNecessary');
                XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
                XG_LayoutHelper::addWelcomeBoxIfNecessary(XG_Layout::load('index'));
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
        }

        if (self::olderThan($currentAppVersion, '3.2')) {
            try {
                self::logSectionStart('grace period keys');
                XG_App::includeFileOnce('/lib/XG_JobHelper.php');
                W_Cache::getWidget('main')->privateConfig[XG_JobHelper::GRACE_PERIOD_KEY] = date('c', time());
                W_Cache::getWidget('main')->privateConfig[XG_SecurityHelper::CSRF_GRACE_PERIOD_KEY] = date('c', time());
                W_Cache::getWidget('main')->saveConfig();
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
        }

        if (self::olderThan($currentAppVersion, '3.3')) {
            try {
                self::logSectionStart('doPre33Migration');
                W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
                Index_AppearanceHelper::doPre33Migration();
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
        }

        if (self::olderThan($currentAppVersion, '3.3')) {
            try {
                self::logSectionStart('membersCanCustomize');
                $widget = W_Cache::getWidget('main');
                $widget->config['membersCanCustomizeTheme'] = 'yes';
                $widget->config['membersCanCustomizeLayout'] = 'yes';
                $widget->saveConfig();
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
        }

        if (self::olderThan($currentAppVersion, '3.3')) {
            try {
                self::logSectionStart('initializeCategoryCountAndActivity');
                XG_JobHelper::start('forum', 'initializeCategoryCountAndActivity');
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
        }

        if (self::olderThan($currentAppVersion, '3.3')) {
            try {
                self::logSectionStart('threadingModel');
                $widget = W_Cache::getWidget('forum');
                $widget->config['threadingModel'] = 'threaded';
                $widget->saveConfig();
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
        }

        if (self::olderThan($currentAppVersion, '3.3.1')) {
            try {
                self::logSectionStart('setClearspringCss');
                W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_ClearspringHelper.php');
                $clearspringCss = Index_ClearspringHelper::getClearspringCssTemplate();
                Index_ClearspringHelper::setClearspringCss($clearspringCss);
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
        }

        try {
            if (self::olderThan($currentAppVersion, '3.3.4')) {
                self::logSectionStart('scheduleDenormalizeFullName');
                /* Kick start the chain of tasks denormalizing fullName by copying from User to GroupMembership to allow sorting/searching on GroupMembership objects. */
                XG_App::includeFileOnce('/widgets/groups/models/GroupMembership.php');
                if (GroupMembership::denormalizeFullName() > 0) {
                    error_log('* creating async job');
                    GroupMembership::scheduleDenormalizeFullName();
                } else {
                    error_log('* skipping async job - not needed');
                }
                self::logSectionEnd();
            }
        } catch (Exception $e) {
            self::logException($e);
        }

        try {
            if (self::olderThan($currentAppVersion, '2.3')) {
                self::logSectionStart('scheduleAddMemberStatus');
                /* Kick start the chain of tasks adding memberStatus property to User to allow sort by status. */
                XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_MembershipHelper.php');
                if (Index_MembershipHelper::addMemberStatus() > 0) {
                    error_log('* creating async job');
                    Index_MembershipHelper::scheduleAddMemberStatus();
                } else {
                    error_log('* skipping async job - not needed');
                }
                self::logSectionEnd();
            }
        } catch (Exception $e) {
            self::logException($e);
        }

        if (self::olderThan($currentAppVersion, '3.3.4')) {
            try {
                self::logSectionStart('buildBlogArchive');
                if (BlogArchive::instance()->my->buildStatus == BlogArchive::NOT_STARTED) {
                    error_log('* creating async job');
                    XG_JobHelper::start('profiles', 'buildBlogArchive');
                } else {
                    error_log('* skipping async job - not needed');
                }
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
            try {
                self::logSectionStart('addAlbumCovers');
                error_log('* creating async job');
                XG_JobHelper::start('photo', 'addAlbumCovers');
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
            try {
                self::logSectionStart('initializeActivityAttributes');
                error_log('* creating async job');
                XG_JobHelper::start('groups', 'initializeActivityAttributes');
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
        }

		if (self::olderThan($currentAppVersion, '3.5')) {
        	try {
                self::logSectionStart('createNewInstance(\'opensocial\')');
                self::createNewInstance('opensocial');
                self::logSectionEnd();
        	} catch (Exception $e) {
            	self::logException($e);
        	}
		}		
		
		if (self::olderThan($currentAppVersion, '3.7')) {
		    //BAZ-8166  Any OpenSocial Gadgets are currently added to networks should be removed once we go live with 0.7
            //BAZ-9163 OpenSocial: Remove Remaining Traces of Gadgets Mozzle
		    try {
                self::logSectionStart('removeGadgetsMozzle');
                self::removeMozzle('gadgets');
                self::logSectionEnd();		        
            } catch (Exception $e) {
	            self::logException($e);
            }
	    }
	    
	    if (self::olderThan($currentAppVersion, '3.7')) {
		    try {
                self::logSectionStart('enableOpenSocial');
                $widget = W_Cache::getWidget('opensocial');
                $widget->privateConfig['isEnabled'] = true;
                $widget->saveConfig();
                self::logSectionEnd();		        
	        } catch (Exception $e) {
	            self::logException($e);
            }
            try {
                self::logSectionStart('privatizeAllWronglyPublicComments');
                error_log('* creating async job');
                XG_JobHelper::start('groups', 'privatizeAllWronglyPublicComments');
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }	        
        }

        // BAZ-7684: Make sure every app has a pages instance
        // the additional args to createNewInstance override the default behavior of creating a first order feature
        try {
            self::logSectionStart('createNewInstance(\'page\')');
            self::createNewInstance('page', null, '', 0);
            self::logSectionEnd();
        } catch (Exception $e) {
            self::logException($e);
        }

        // This routine has no effect if called unnecessarily and needs to be called
        // whenever a new widget type is added to Bazel.  So we call it on every appWideCodeUpgrade.
        try {
            self::logSectionStart('updateAddFeaturesSortOrder');
            XG_App::includeFileOnce("/lib/XG_ConfigHelper.php");
            XG_ConfigHelper::updateAddFeaturesSortOrder();
            self::logSectionEnd();
        } catch (Exception $e) {
            self::logException($e);
        }

        if (self::olderThan($currentAppVersion, '3.5.1')) {
            try {
                self::logSectionStart('XG_FacebookHelper::migrateConfig');
                XG_App::includeFileOnce('/lib/XG_FacebookHelper.php');
                $r = XG_FacebookHelper::migrateConfig();
                error_log($r ? 'facebook config migrated' : 'no facebook config migration required');
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
        }

        if (self::olderThan($currentAppVersion, '3.5.5.3')) {
            try {
                self::logSectionStart('Update PAGE widget (BAZ-9228)');
                $widget = W_Cache::getWidget('page');
                $widget->config['isPermanent'] = '1';
                $widget->saveConfig();
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
        }

        if (self::olderThan($currentAppVersion, '3.5.6')) {
            // generate chat instance (BAZ-9263) [ywh 2008-08-26]
            try {
                self::logSectionStart('createNewInstance(\'chat\')');
                self::createNewInstance('chat');
                self::logSectionEnd();
            } catch (Exception $e) {
                self::logException($e);
            }
        }

        try {
            // Pick up new lib and model files [Jon Aquino 2008-05-15]
            XG_ConfigCachingApp::rebuildData();
        } catch (Exception $e) {
            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
        }

        if (self::olderThan($currentAppVersion, '3.6')) {
            try {
                // Add blog tab if the feature is enabled
                self::logSectionStart('Add new Blogs Tab (BAZ-8920) if necessary');

                XG_App::includeFileOnce('/lib/XG_LayoutHelper.php');
                $addBlogTab = XG_LayoutHelper::hasEmbed(XG_Layout::load('index')->getLayout(), 'profiles', array('embed1','embed2'));

                // update configs for legacy tabs (BAZ-8920)
                $profilesWidget = W_Cache::getWidget('profiles');
                $profilesWidget->config['showBlogsTab'] = $addBlogTab ? 1 : 0;
                $profilesWidget->saveConfig();

                XG_App::includeFileOnce('/lib/XG_TabLayout.php');
                if (XG_TabLayout::isEnabled()) {
                    // BAZ-9866: Add Blog tab if necessary for tab manager
                    if ($addBlogTab) {
                        // do we care if the NC manually created a blog tab already? [ywh 2008-09-13]
                        $tabLayout = XG_TabLayout::loadOrCreate(false);
                        if ($tabLayout !== NULL) {
                            $tabLayout->insertTabBefore('manage', 'blogs', $profilesWidget->buildUrl('blog', 'list'), xg_text('BLOGS_TAB_TEXT'))->save();
                        }
                    } else {
                        // do nothing for tab manager
                    }
                }

                self::logSectionEnd();

                // Update feature ordering for existing apps (BAZ-10103) [ywh 2008-09-18]
                self::logSectionStart('Updating feature ordering (BAZ-10103)');
                XG_App::includeFileOnce('/lib/XG_ConfigHelper.php');
                XG_ConfigHelper::resetAddFeaturesSortOrder();
                self::logSectionEnd();
            } catch (Exception $e) {
            	self::logException($e);
            }
        }

        error_log('appWideCodeUpgrade end');
    }

    /**
     * Logs a note that the current appWideCodeUpgrade section has finished.
     */
    private static function logSectionStart($name) {
        error_log('awcu section start: ' . $name);
    }

    /**
     * Logs a note that the current appWideCodeUpgrade section has finished.
     */
    private static function logSectionEnd() {
        error_log('awcu section end');
    }

    /**
     * Records the exception in the error.log file.
     *
     * @param $e Exception  the exception to log
     */
    private static function logException($e) {
        error_log($e->getMessage() . "\n" . $e->getTraceAsString());
    }

    /**
     * Returns whether version A is older than version B.
     *
     * @param $a string  a version string, e.g., 2.0:2092
     * @param $b string  a version string, e.g., 2.0:2092
     */
    protected static function olderThan($a, $b) {
        return self::compareByReleaseAndRevision($a, $b) < 0;
    }

    /**
     * Sets the attributes on the given shapes, saving the shapes if they change.
     *
     * @param $data array  data keyed by shape name, attribute name, "type", and "indexing"
     */
    protected static function setShapeAttributes($data, $shapeClass = 'XN_Shape') {
        foreach ($data as $shapeName => $shapeData) {
            $shape = call_user_func(array($shapeClass, 'load'), $shapeName);
            if (! $shape) { continue; }
            $shapeChanged = false;
            foreach ($shapeData as $attributeName => $attributeData) {
                if ($shape->attributes[$attributeName] && ! $attributeData['indexing']) { continue; }
                if ($shape->attributes[$attributeName] && $attributeData['indexing'] == $shape->attributes[$attributeName]->indexing) { continue; }
                $shape->setAttribute($attributeName, $attributeData);
                $shapeChanged = true;
            }
            if ($shapeChanged) { $shape->save(); }
        }
    }

    /**
     *  Add a crossdomain.xml file at the root if there is none
     **/
    protected static function createCrossDomainXml(){
        if (file_exists(NF_APP_BASE . "/crossdomain.xml")) return;

        $crossdomainXML = <<<CROSS_DOMAIN_XML
<?xml version="1.0"?>
<cross-domain-policy>
<allow-access-from domain="*"/>
</cross-domain-policy>
CROSS_DOMAIN_XML;

        file_put_contents(NF_APP_BASE . "/crossdomain.xml",$crossdomainXML);
    }

    /**
     * Copy any existing newgrid.css into custom CSS and rename the existing newgrid.css to newgrid.css.old
     *
     * A one-off upgrade for 3.0 networks moving to new grid permanently from trial run version.
     */
    protected static function upgradeToNewGrid() {
        if (! XG_Cache::lock('upgrade-to-new-grid')) { return; }
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
        $NEW_GRID_PATH = NF_APP_BASE . "/newgrid.css";
        $newGridCss = @file_get_contents($NEW_GRID_PATH);
        if (! $newGridCss) { return; }
        $customCssPath = Index_AppearanceHelper::getCustomCssFilename();
        $customCss = file_get_contents($customCssPath);
        $newCustomCss = $newGridCss . "\n" . $customCss;
        file_put_contents(Index_AppearanceHelper::getCustomCssFilename(), $newCustomCss);
        file_put_contents($NEW_GRID_PATH . ".old", $newGridCss);
        unlink($NEW_GRID_PATH);
    }

    /**
     * Move CSS files (theme<n>.css & custom.css) to xn_resources for better
     *   performance (BAZ-2109) if they haven't been moved yet
     */
    protected static function moveCssFiles() {
        $widget = W_Cache::getWidget('main');
        if (!($widget->config['customCssVersion'])) {
            $widget->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
            $widget->config['customCssVersion'] = (float) 1;
            $widget->saveConfig();

            @mkdir(dirname(Index_AppearanceHelper::getThemeCssFilename()));
            @rename($_SERVER['DOCUMENT_ROOT'] . '/theme'
                    . $widget->config['userCssVersion'] . '.css',
                    Index_AppearanceHelper::getThemeCssFilename());
            @mkdir(dirname(Index_AppearanceHelper::getCustomCssFilename()));
            @rename($_SERVER['DOCUMENT_ROOT'] . '/custom.css',
                    Index_AppearanceHelper::getCustomCssFilename());
        }
    }

    /**
     *  Create an instance of a mozzle if one doesn't already exist.
     *
     * @param $name Name of both the instance and the widget
     * @param $extraConfigXml  Extra XML to insert into the <config> section
     * @param $isFirstOrder
     * @param $isPermanent
     * @param $isEnabledDefault
     */
    public static function createNewInstance($name, $root = null, $extraConfigXml = '', $isFirstOrder = 1,
            $isPermanent = 0, $isEnabledDefault = 0) {
        if (is_null($root)) { $root = $name; }
        XG_App::includeFileOnce('/lib/XG_ModuleHelper.php');
        //  Check for a preexisting instance
        //  Can't just getWidget - it throws an exception on failure
        $moduleInfo = XG_ModuleHelper::getAllModules();
        if (isset($moduleInfo[$name])) {
            return;
        }

        //  No instance - create one
        @mkdir(NF_APP_BASE . "/instances/$name");
        //  Find a free instance ID
        $newInstanceId = 47;
        $takenIds = array();
        foreach ($moduleInfo as $module) {
            $takenIds[$module->id] = TRUE;
        }
        while ($takenIds[$newInstanceId]) {
            $newInstanceId++;
        }

        $upcaseName = ucwords($name);
        $newInstanceXML = <<<NEW_INSTANCE_XML
<?xml version="1.0"?>
<widget id="$newInstanceId" root="$root">
    <name>$name</name>
    <config>
        <version type="string" checkState="true">0.1</version>
        <title>$upcaseName</title>
        <displayName>$upcaseName</displayName>
        <description></description>
        <isMozzle type="number">1</isMozzle>
        <isFirstOrderFeature type="number">$isFirstOrder</isFirstOrderFeature>
        <isPermanent type="number">$isPermanent</isPermanent>
        <isEnabledDefault type="number">$isEnabledDefault</isEnabledDefault>
        $extraConfigXml
    </config>
    <privateConfig>
        <isEnabled/>
    </privateConfig>
</widget>
NEW_INSTANCE_XML;

        file_put_contents(NF_APP_BASE . "/instances/$name/widget-configuration.xml",
                $newInstanceXML);
    }
    
    /**
     * Disable and remove the widget configuration and the private configuration for the specified mozzle.
     *
     * @param   $name   string  Name of the mozzle to remove.
     * @return          void
     */
    public function removeMozzle($name) {

        // Some vars we're going to need
        $configDir = NF_APP_BASE . "/instances/$name";
        $configFile = "$configDir/widget-configuration.xml";
        $privateConfigFile = NF_APP_BASE . "/xn_private/$name-private-configuration.xml";

        $moduleInfo = XG_ModuleHelper::getAllModules();
        if (! file_exists($configFile)) {
            return;
        }
        
        try {
            $widget = W_Cache::getWidget($name);
        } catch (Exception $e) {
            error_log("Unable to get $name widget, ending removal.");
            return;
        }
        // Be as sure as we can that this is /our/ mozzle not something the user has created.
        if (! $widget || ! $widget->config || $widget->config['isMozzle'] != "1" || $widget->root != $name) {
            error_log("$name mozzle seemed to be a user creation not ours so not removing it.");
            return;
        }
        
        // Disable first so that if anything goes wrong with the removal the mozzle is still gone
        $widget->privateConfig['isEnabled'] = false;
        $widget->config['isEnabledDefault'] = false;
        $widget->saveConfig();
        
        // Remove instances subdir and private config xml
        ini_set('display_errors', 0);
        try {
            if (! rename($configDir, NF_APP_BASE . "/xn_private/xn_volatile/$name." . mt_rand())) {
                error_log("Unable to move $configDir to xn_volatile when removing $name mozzle");
            } 
            if (! rename($privateConfigFile, NF_APP_BASE . "/xn_private/xn_volatile/$name-private-configuration.xml." . mt_rand())) {
                error_log("Unable to move $privateConfigFile to xn_volatile when removing $name mozzle");
            } 
        } catch (Exception $e) {
            error_log("Problem while renaming when removing $name mozzle: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        }

        // Remove the tab for the mozzle.
        XG_App::includeFileOnce('/lib/XG_TabLayout.php');
        if (XG_TabLayout::isEnabled()) {
            $tabLayout = XG_TabLayout::loadOrCreate(false);
            if ($tabLayout !== NULL) {
                $tabLayout->removeTab($name)->save();
            }
        }

        ini_set('display_errors', 1);
    }

    /**
     * Initialize the 1.8-style badge and player appearance configuration
     *
     * Does nothing if such configuration already exists unless:
     *
     * @param force Overwrite config even if it already exists
     */
    public static function createBadgeAndPlayerConfig($force = false) {
        XG_App::includeFileOnce('/lib/XG_FileHelper.php');
        if (!$force) {
            //  Look for embeds_* variables in our private config
            //  If there are any, migration has already been done - return
            $widget = W_Cache::getWidget('main');
            foreach ($widget->privateConfig as $key => $value) {
                if ((mb_substr($key, 0, 7) == 'embeds_') && mb_strlen($value)) {
                    return;
                }
            }
        }

        $photoWidget = W_Cache::getWidget('photo');
        $photoBgColor = $photoWidget->privateConfig['playerHeaderBackground'];
        $photoLogoType = $photoWidget->privateConfig['playerLogoType'];

        $videoWidget = W_Cache::getWidget('video');
        $videoBgColor = $videoWidget->privateConfig['playerHeaderBackground'];
        $videoLogoType = $videoWidget->privateConfig['playerLogoType'];

        //  Preserve the video watermark if there is one
        $videoWatermark = ($videoLogoType == 'watermark_image'
                ? $videoWidget->privateConfig['playerLogoUrl'] : '');
        //  Use video logo, then photo logo, on badges
        $badgeLogo = ($videoLogoType == 'header_image'
                ? $videoWidget->privateConfig['playerLogoUrl'] :
                ($photoLogoType == 'header_image'
                ? $photoWidget->privateConfig['playerImageUrl'] : ''));
        //  add dimensions if necessary
        if ($videoWatermark) {
            $videoWatermark = XG_FileHelper::setImageUrlDimensions($videoWatermark);
        }
        if ($badgeLogo) {
            $badgeLogo = XG_FileHelper::setImageUrlDimensions($badgeLogo);
        }

        XG_App::includeFileOnce('/lib/XG_EmbeddableHelper.php');
        $widget = W_Cache::getWidget('main');
        $ningbarColor = '';
        if (array_key_exists('ningbarColor', $widget->config)) {
            $ningbarColor = $widget->config['ningbarColor'];
        } else {
            $widget->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
            Index_AppearanceHelper::getAppearanceSettings(NULL, $themeColors, $themeImagePaths);
            $ningbarColor = $themeColors['ningbarColor'];
        }
        $settings = array();
        $settings['embeds_backgroundColor'] = ($videoBgColor ? $videoBgColor
                : ($photoBgColor ? $photoBgColor
                : $ningbarColor));
        if ($videoWatermark) {
            $settings['embeds_playerLogoImageUrl'] = $videoWatermark;
        } else {
            $settings['embeds_displayNameInPlayer'] = 'Y';
            //  Set text color based on Ningbar color rules
            $bgColor = $settings['embeds_backgroundColor'];
            $red = hexdec(mb_substr($bgColor, 1, 2));
            $green = hexdec(mb_substr($bgColor, 3, 2));
            $blue = hexdec(mb_substr($bgColor, 5, 2));
            $value = ($red + $green + $blue) / 3;
            if ($value > 150) {
                $settings['embeds_networkNameColor'] = '333333';
            } else {
                $settings['embeds_networkNameColor'] = 'EEEEEE';
            }
        }
        $settings['embeds_badgeLogoImageUrl'] = $badgeLogo;
        XG_EmbeddableHelper::setEmbedCustomization($settings);
    }

    /**
     * Migrate privacy settings to the new 2.0 scheme
     */
    protected static function migratePrivacySettings() {
        $widget = W_Cache::getWidget('main');
        /* If the new config values have already been set, then there's
         * no need to migrate again */
        if (mb_strlen($widget->config['allowJoin']) > 0) {
            return;
        }
        if (XG_App::appIsPrivate()) {
            $allowInviteRequests = $widget->config['allowRequests'] == 'yes';
            /* Network set to Private with both the "members can invite other
             * members" and the "visitors can request an invite" options checked
             * =>
             * Network should stay Private, with "Anyone can join" as the
             * selected option, and with Member Moderation turned on */
            if (XG_App::allowInvites() && $allowInviteRequests) {
                $widget->config['allowJoin'] = 'all';
                $widget->config['moderateMembers'] = 'yes';
            }
            /* Network set to Private with the "members can invite other
             * members" option checked
             * =>
             * Network should stay Private, with "Invite Only" as the selected
             * option, and with Member Moderation turned off */
            else if (XG_App::allowInvites()) {
                $widget->config['allowJoin'] = 'invited';
                $widget->config['moderateMembers'] = 'no';
            }
            /* Network set to Private with the "visitors can request an invite"
             * option checked
             * =>
             * Network should stay Private, with "Anyone can join" as the
             * selected option, and with Member Moderation turned on */
            else if ($allowInviteRequests) {
                $widget->config['allowJoin'] = 'all';
                $widget->config['moderateMembers'] = 'yes';
            }
            /* Network set to Private, with no sub-options checked
             * =>
             * Network should stay Private, with "Invite Only" as the selected
             * option and with Member Moderation turned off */
            else {
                $widget->config['allowJoin'] = 'invited';
                $widget->config['moderateMembers'] = 'no';
            }
        }
        /* Network set to Public
         * =>
         * Settings carry over, all can join, member moderation is off */
        else {
            $widget->config['allowJoin'] = 'all';
            $widget->config['moderateMembers'] = 'no';
        }
        /* These settings are no longer used, so lock them into the correct
         * values */
        $widget->config['allowInvites'] = 'yes';
        $widget->config['allowRequests'] = 'no';
        $widget->saveConfig();
    }

    /**
     * Appends the app's current version to the URL. Useful for ensuring that the browser is
     * getting the the latest swf (rather than an old swf in its cache), which is important for pages
     * within the app (less important for Facebook and external websites).
     *
     * @param $url string  the URL to which to append the version
     * @return string  the URL with app-version appended, e.g., http://example.org?v=1.8
     */
    public static function addVersionParameter($url) {
        return XG_HttpHelper::addParameter($url, 'v', self::currentCodeVersion());
    }

    /**
     * Appends the app's current version to the URL.
     *
     * @param $url string  the URL to which to append the version
     * @return string  the URL with revision number appended, e.g., http://example.org?xn_version=1.8_2028
     */
    public static function addXnVersionParameter($url) {
        return XG_HttpHelper::addParameter($url, 'xn_version', str_replace(':', '_', self::currentCodeVersion()));
    }
}
