<?php
/**
 * Useful functions for unit testing.
 */
class XG_TestHelper {

    /**
     * Regular expression for matching nested parentheses.
     *
     * @see Jeffrey Friedl, "Mastering Regular Expressions", 3rd ed., p. 476,
     *     Recursive reference to a set of capturing parentheses.
     */
    const NESTED_PARENTHESES_PATTERN = '((?:[^()]++|\((?1)\))*)';

    /**
     * Regular expression for matching nested curly brackets.
     *
     * @see Jeffrey Friedl, "Mastering Regular Expressions", 3rd ed., p. 476,
     *     Recursive reference to a set of capturing parentheses.
     */
    const NESTED_CURLY_BRACKETS_PATTERN = '((?:[^{}]++|\{(?1)\})*)';

    /**
     * Makes the specified widget the current widget.
     * Typically called in a test class's setUp() method.
     *
     * @param $instanceName string  The name of the widget instance
     */
    public static function setCurrentWidget($instanceName) {
        TestApp::setRequestedRoute(array('widgetName' => $instanceName));
        W_Cache::push(W_Cache::getWidget($instanceName));
    }

    /**
     * Marks the given object as being for testing only.
     *
     * @param XN_Content|W_Content  The object
     * @return XN_Content|W_Content  The object
     */
    public static function markAsTestObject($object) {
        // Unwrap W_Content to bypass content shape check [Jon Aquino 2007-01-25]
        $objectProper = $object instanceof XN_Content ? $object : W_Content::unwrap($object);
        $objectProper->my->test = 'Y';
        // Store current URL to help debug TestObjectsDeletedTest failures [Jon Aquino 2008-01-02]
        $objectProper->my->testUrl = XG_HttpHelper::currentUrl();
        return $object;
    }

    /**
     * Returns whether the specified object exists.
     *
     * @param $id string  ID of the object to check
     * @return boolean  Whether the object exists
     */
    public function exists($id) {
        try {
            return XN_Content::load($id) ? TRUE : FALSE;
        } catch (Exception $e) {
            return FALSE;
        }
    }

    /**
     * Filters out the IDs of non-existent content objects.
     *
     * @param $ids array  Content-object IDs to check
     * @return array  The IDs of content objects that exist
     */
    public function existingIds($ids) {
        $existingIds = array();
        foreach ($ids as $id) {
            if (self::exists($id)) { $existingIds[] = $id; }
        }
        return $existingIds;
    }

    /**
     * Deletes content objects that have been marked as test objects.
     * Typically called in a test class's tearDown() method.
     */
    public static function deleteTestObjects() {
        // Suppress "Groups IDs do not match" warnings [Jon Aquino 2007-04-24]
        unset($_GET['id']);
        unset($_GET['groupId']);

        $q = XN_Query::create('Content');
        $q->filter('owner');
        $q->filter('my.test', '=', 'Y');
        foreach ($q->execute() as $x) {
            XN_Content::delete($x);
        }
        //TODO: Would we greatly improve the speed of tests if we could remove this query?
        // Seems that it must be called hundreds or even thousands of times in a test run.
        // Can we not mark these objects with my.test = Y? [Thomas David Baker 2008-03-12]
        $q = XN_Query::create('Content');
        $q->filter('owner');
        $q->filter('my.attachedToType', '=', 'Food');
        foreach ($q->execute() as $x) {
            XN_Content::delete($x);
        }
    }

    /**
     * Recursive version of glob
     *
     * @param $sDir string      Directory to start with.
     * @param $sPattern string  Pattern to glob for.
     * @param $nFlags int      Flags sent to glob.
     * @return array containing all pattern-matched files.
     */
    // From http://ca3.php.net/manual/en/function.glob.php#30238  [Jon Aquino 2007-01-17]
    public static function globr($sDir, $sPattern, $nFlags = NULL) {
        static $excludedFiles = null;
        if (is_null($excludedFiles)) {
            $excludedFiles = array(
                $_SERVER['DOCUMENT_ROOT'] . '/runner.php',
                $_SERVER['DOCUMENT_ROOT'] . '/xn_private/xn_volatile/error.log',
            );
        }
        $sDir = escapeshellcmd($sDir);

        // Get the list of all matching files currently in the
        // directory.
        $aFiles = glob("$sDir/$sPattern", $nFlags);

        // Then get a list of all directories in this directory, and
        // run ourselves on the resulting array.  This is the
        // recursion step, which will not execute if there are no
        // directories.
        foreach (glob("$sDir/*", GLOB_ONLYDIR) as $sSubDir) {
            $aSubFiles = self::globr($sSubDir, $sPattern, $nFlags);
            $aFiles = array_merge($aFiles, $aSubFiles);
        }

        // Remove any files that are suppposed to be excluded
        foreach ($excludedFiles as $excludedFile) {
            $key = array_search($excludedFile, $aFiles);
            if ($key === false) {
                continue;
            }
            unset($aFiles[$key]);
        }

        // The array we return contains the files we found, and the
        // files all of our children found.
        return $aFiles;
    }

    /**
     * Creates a Topic object
     *
     * @param $title string  Title for the Topic object (defaults to "test title")
     * @return array  The ID of the Topic, and the Topic itself
     */
    public static function createTopic($title = 'test title') {
        self::setCurrentWidget('forum');
        $topic = XG_TestHelper::markAsTestObject(Topic::create($title, 'test description'));
        $topic->save();
        return array($topic->id, $topic);
    }

    /**
     * Creates an UploadedFile object and attaches it to the given object
     *
     * @param $attachedTo XN_Content|W_Content  The object to attach the UploadedFile to
     * @return array  The ID of the UploadedFile, and the UploadedFile itself
     */
    public static function createAttachment($attachedTo) {
        $attachment = XG_TestHelper::markAsTestObject(XN_Content::create('UploadedFile'));
        $attachment->my->mozzle = W_Cache::current('W_Widget')->dir;
        $attachment->save();
        Forum_FileHelper::addAttachmentProper($attachment->id, 'apple.txt', 123, $attachedTo);
        $attachedTo->save();
        return array($attachment->id, $attachment);
    }

    /**
     * Creates a Comment object
     *
     * @param $attachedTo XN_Content|W_Content  The object being commented on
     * @param $parentComment XN_Content|W_Content  The Comment's parent Comment, if any
     * @return array  The ID of the Comment, and the Comment itself
     */
    public static function createComment($attachedTo, $parentComment = NULL) {
        XG_App::includeFileOnce('/widgets/forum/lib/helpers/Forum_CommentHelper.php');
        $comment = XG_TestHelper::markAsTestObject(Forum_CommentHelper::createComment($attachedTo, 'Test comment', $parentComment));
        $comment->save();
        return array($comment->id, $comment);
    }

    /**
     * Returns arrays about JavaScript dependencies among the .php and .js files.
     *
     * @return array  filename => required module names, required module name => filenames,
     *         filename => provided module names, provided module name => filenames
     */
    public static function buildDependencyGraph() {
        if (! self::$fileToRequires) {
			$files = array_merge(XG_TestHelper::globr(NF_APP_BASE, '*.php'), XG_TestHelper::globr(NF_APP_BASE, '*.js'));
            foreach($files as $file) {
            	unset($contents);
          if (preg_match('/test/', substr($file, strlen(NF_APP_BASE)))) { continue; }
                $contents = str_replace('.en_US', '', str_replace(".' . XG_LOCALE", "'", str_replace(".' + xg.global.locale", "'", file_get_contents($file))));
                $contents = str_replace('\'xg.shared.messagecatalogs.<%= XG_LOCALE %>\', ', '', $contents);
                $contents = str_replace('xg.custom.shared.messagecatalogs', '', $contents);
                if (strpos($file, 'util.js') !== false) { $contents = preg_replace('@dojo.require..xg.shared.nls@', '', $contents); }
                if (! preg_match('/(dojo|ningLoader).*require|dojo.provide/i', $contents)) { continue; }
                foreach (explode("\n", $contents) as $line) {
                    if (preg_match('/dojo.require\(|ningLoaderRequire\(/', $line)) {
                        preg_match_all('/xg\.[a-z0-9._]+/i', $line, $matches);
                        foreach ($matches[0] as $match) {
                            self::$fileToRequires[$file][] = $match;
                            self::$requireToFiles[$match][] = $file;
                        }
                    }
                    if (preg_match('/dojo.provide/', $line)) {
                        preg_match_all('/xg\.[a-z0-9._]+/i', $line, $matches);
                        foreach ($matches[0] as $match) {
                            self::$fileToProvides[$file][] = $match;
                            self::$provideToFiles[$match][] = $file;
                        }
                    }
                }
            }
        }
        return array(self::$fileToRequires, self::$requireToFiles, self::$fileToProvides, self::$provideToFiles);
    }

            // Find one user as an admin for this network @TODO: do this everywhere we have 'JonathanAquino' as a test case.
    public static function getTwoUsers() {
        $users = User::find(array(), 0, 2);
        $current = XN_Profile::current()->screenName;
        $other = '';
        foreach ($users['users'] as $u) {
            if ($u->title !== $current) {
                $other = $u->title;
                break;
            }
        }
        return array('current' => $current, 'other' => $other);
    }
    
    /** An array of filename => required module names */
    private static $fileToRequires;

    /** An array of required module name => filenames */
    private static $requireToFiles;

    /** An array of filename => provided module names */
    private static $fileToProvides;

    /** An array of provided module name => filenames */
    private static $provideToFiles;

    /**
     * Extracts most of the I18N keys from the given JavaScript I18N file.
     *
     * @param $javascriptFile string  Path to a JavaScript I18N file, e.g., /apps/8/2AA/05E/devbazjon6/xn_resources/widgets/shared/js/messagecatalogs/en_US.js
     * @return array  message name => message text
     */
    public static function javascriptI18NKeys($javascriptFile) {
        XG_App::includeFileOnce('/widgets/index/lib/helpers/Index_MessageCatalogReader.php');
        $reader = new Index_MessageCatalogReader();
        $reader->read(file_get_contents($javascriptFile));
        return self::partitionJavaScriptMessagesByNamespace($reader->getData());
    }

    /**
     * Groups the messages by their package names (e.g. xg.feed.nls)
     *
     * @param $messages array  message texts keyed by fully qualified message name (e.g. xg.feed.nls.cancel)
     * @return array  namespace => [message name => message text]
     */
    private static function partitionJavaScriptMessagesByNamespace($messages) {
        $partitionedMessages = array();
        foreach ($messages as $name => $message) {
            if (! preg_match('@^(.*)\.([^.]*)$@u', $name, $matches)) { throw new Exception('Could not parse name: ' . $name); }
            $partitionedMessages[$matches[1]][$matches[2]] = $message;
        }
        return $partitionedMessages;
    }

    /**
     * Extracts most of the I18N keys from the given PHP I18N file.
     *
     * @param $phpFile string  Path to a PHP I18N file, e.g., /apps/8/2AA/05E/devbazjon6/lib/XG_MessageCatalog_en_US.php
     * @return array  key => line
     */
    public static function phpI18NKeys($phpFile) {
        if (! file_exists($phpFile)) { return array(); }
        $messages = array();
        $contents = file_get_contents($phpFile);
        foreach (explode("\n", $contents) as $line) {
            if (preg_match("/'(.+)' *=>/", $line, $matches)) {
                $messages[$matches[1]] = $line;
            }
            if (preg_match("/'([A-Z_]+)'/", $line, $matches)) {
                $messages[$matches[1]] = $line;
            }
        }
        // Get all the lines of multiline-messages [Jon Aquino 2007-06-01]
        require_once $phpFile;
        preg_match('@(XG_MessageCatalog.*).php@', $phpFile, $matches);
        $messageCatalog = new $matches[1];
        foreach ($messageCatalog->getMessagesForTesting() as $name => $message) {
            $messages[$name] = '\'' . $name . '\' => \'' . $message . '\',';
        }
        return $messages;
    }

    /**
     * Called before a content object is saved.
     *
     * @param $content XN_Content  The content object
     */
    public static function beforeSave($content) {
        if (! defined('UNIT_TESTING')) { return; }
        if ($content->id) {
            // Don't mark existing objects for deletion (e.g. User objects!)
            return;
        }
        self::markAsTestObject($content);
    }

    /**
     * Displays the value of each byte in the string.
     *
     * @param $s string  the string to examine
     * @return string  the byte values
     */
    public static function bytes($s) {
        $bytes = '';
        for ($i = 0; $i < strlen($s); $i++) {
            $bytes .= $s[$i] . ':' . ord($s[$i]);
        }
        return $bytes;
    }

    /**
     * Detects parse errors without executing the code.
     *
     * @param $code  string  the code to check
     * @return  boolean  whether the code compiles
     */
    public static function checkSyntax(&$code) {
        // Idea from nicolas.grekas+php@gmail.com, http://php.net/php_check_syntax
        $code = preg_replace('@^<@ui', '?><', $code);
        $code = preg_replace('@>$@u', '><?php', $code);
        return eval("if(0){\n" . $code . "\n}") !== false;
    }

    /**
     * Creates a test user with the specified details.  Be sure to call deleteTestObjects
     * in the tearDown method of your test case to remove the test user from the
     * content store.
     */
    public static function createTestUser($username, $activityCount = null, $blocked = false) {
        $user = XN_Content::create('User', $username);
        if (is_null($user)) { throw new Exception("Did not create test user"); }
        $user->my->xg_forum_activityCount = $activityCount;
        $user->my->xg_video_activityCount = $activityCount;
        $user->my->xg_photo_activityCount = $activityCount;
        $user->my->profileAddress = $username . "ProfileAddress";
        $user->my->isAdmin = 'N';
        $user->my->fullName = "Dr. " . $username . " the Fourth";
        if ($blocked) { $user->my->xg_index_status = 'blocked'; }
        $user->save();
        return $user;
    }

    /**
     * Returns the IDs of the given content objects.
     *
     * @param $objects array  array of XN_Content and W_Content objects
     * @return array  the content IDs
     */
    public static function ids($objects) {
        $ids = array();
        foreach ($objects as $object) {
            $ids[] = $object->id;
        }
        return $ids;
    }

    /**
     * Returns the titles of the given content objects.
     *
     * @param $objects array  array of XN_Content and W_Content objects
     * @return array  the titles
     */
    public static function titles($objects) {
        $titles = array();
        foreach ($objects as $object) {
            $titles[] = $object->title;
        }
        return $titles;
    }

    /**
     * Creates a fake XN_Profile with the given screen name
     *
     * @param $screenName string  the value for the profile's screenName attribute
     * @return XN_Profile  the fake profile object
     */
    public static function createProfile($screenName) {
        $profile = XN_Profile::create($screenName . '@foo.com', 'password');
        MyTestProfile::setScreenName($profile, $screenName);
        return $profile;
    }

    /**
     * Convert the specified PHP XML object to a string representation, html escape it and print in <pre> tags.
     * Lots better than var_dump or print_r.
     *
     * @param   $xml    <xml object>    XML object to print.
     * @return          void
     */
    public static function printXml($xml) {
        $s = self::xmlToString($xml);
        print "<pre>" . htmlentities($s) . "</pre>";
    }

    /**
     * Take some arbitrary PHP XML object and produce a useful string representation of it.
     *
     * @param   $xml    <xml object>    XML object to transform.
     * @return          string          String of the XML represented by the object.
     */
    public static function xmlToString($xml) {
        if ($xml instanceof DOMDocument) {
            $s = $xml->saveXML();
        } else if ($xml instanceof DOMNodeList) {
            $s = '';
            foreach ($xml as $node) { $s .= self::xmlToString($node); }
        } else if (is_null($xml)) {
            $s = '[null]';
        } else {
            $s = $xml->ownerDocument->saveXml($xml);
        }
        return $s;
    }
}

/**
 * Provides access to XG_App's protected functions
 */
if (class_exists('XG_App')) { // We don't currently load this class for the /test/js unit tests. [Jon Aquino 2008-08-29]
class TestApp extends XG_App {
    /**
     * Sets the value of $requestedRoute. For unit-testing only.
     */
    public static function setRequestedRoute($requestedRoute) {
        parent::setRequestedRouteForTesting($requestedRoute);
    }
}
}

/**
 * Provides access to XN_Profile's protected data.
 */
class MyTestProfile extends XN_Profile {
    /**
     * Sets the value of the profile's screen name.
     *
     * @param $profile XN_Profile  the profile to modify
     * @param $screenName string  the value for the profile's screenName attribute
     */
    public static function setScreenName($profile, $screenName) {
        $profile->_data['screenName'] = $screenName;
    }
}

XN_Event::listen('xn/content/save/before', array('XG_TestHelper', 'beforeSave'));
