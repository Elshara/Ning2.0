<?php

/**
 * TODO this has been an option under manage since 1.9 (?) so we should remove this now outdated script, I think.
 *
 * Make a private app public. (see BAZ-1140). This script is in its own file
 * so that we can easily remove it after launch and its work is done.
 */
 
 
define('NF_APP_BASE',$_SERVER['DOCUMENT_ROOT']);
define('NF_BASE_URL', '');

/* Load the base WWF code */
require_once XN_INCLUDE_PREFIX .'/WWF/bot.php';

/* Load our custom App class */
W_WidgetApp::includeFileOnce('/lib/XG_App.php');
/* Content and profile caching */
XG_App::includeFileOnce('/lib/XG_Cache.php');
/* Query result caching */
XG_App::includeFileOnce('/lib/XG_Query.php');
/* Load the app's widgets. We'll need this to tweak the configuration settings
 * after all the content objects have been altered. */
XG_App::loadWidgets();


/*
 * 1. If the app is already public, don't do anything.
 * 2. Are you sure you want to make this app public?
 * 2a. If not, exit
 * 2b. If so, continue
 * 3. Find some private objects whose type is not in the exclusion list
 * 4. Print status info
 * 5. Make the objects public
 * 6. Are there more objects to make public?
 * 6a. If not, change the appPrivacy config setting and print confirmation message
 * 6b. If so, reload the page with a param to skup to step 3.
*/ 

XN_Debug::allowDebug(); $_GET['xn_debug'] = 'api-comm';
XG_Publickifier::dispatch();



class XG_Publickifier {
     
     /** How many content objects to change per request */
     protected static $objectsPerRequest = 30;
    
     protected static $exclude = array('Comment','Invitation','TopicCommenterLink','PageCommenterLink');
     protected static $excludeOnVisibility = array('Photo','Video','BlogPost');
     protected static $excludeOnLinkedVisibility = array('VideoAttachment' => 'video', 'VideoPreviewFrame' => 'video');
     
     public static function dispatch() {
         $user = XN_Profile::current();
        if (! ($user->isLoggedIn() && ($user->screenName == 'NingDev'))) {
            print "For administrative use only.";
            exit();
        }
        
        if (! XG_App::appIsPrivate()) {
            print "This app is already public.";
            exit();
        }
        
        if (isset($_GET['reload']) && ($_GET['reload'] == 'true')) {
            XG_Publickifier::makeObjectsPublic();
        } else {
        print<<<_HTML_
        
        Do you want to make this app public? The process will take a few minutes. Leave this
        browser window open until the process completes.
        
        <br/>
        
        <a href="{$_SERVER['SCRIPT_URI']}?reload=true">Yes, Make this app public</a>
        <br/>
        <br/>
        <a href="/">No, I don't want to make this app public.</a>
_HTML_;
        }
     }
     
     protected static function makeObjectsPublic() {
         $moreToDo = self::makeObjectsPublicProper();
         
         if ($moreToDo) {
             print "More objects to change.";
             $url = '' . $_SERVER["SCRIPT_URI"] . '?reload=true&cache=' . md5(uniqid());
             // print "<script>window.location.href='$url';</xscript>";
             print "<a href='$url'>click to continue</a>";
         } else {
             self::updateConfig();
             print "All done. This app is now public.";
         }
     }
     
     /**
      * Makes some content objects public.
      * Returns true if there is more to do, false if not
      */
     protected static function makeObjectsPublicProper() {
         $changedObjects = 0;
         
         $regularInfo = self::queryRegular(self::$objectsPerRequest);
         foreach ($regularInfo['objects'] as $object) {
             $object->isPrivate = false;
             $object->save();
             $changedObjects++;
         }
         
         if ($changedObjects >= self::$objectsPerRequest) {
             return true;
         }
         
         $excludedInfo = self::queryExcludeOnVisibility(self::$objectsPerRequest - $changedObjects);
         foreach ($excludedInfo['objects'] as $object) {
             $object->isPrivate = false;
             $object->save();
             $changedObjects++;
             if ($object->type == 'Video') {
                 foreach (self::$excludeOnLinkedVisibility as $type => $attr) {
                     $query = XN_Query::create('Content')->filter('owner')->filter('type','eic',$type)->filter("my->$attr",'=',$object->id)->filter('isPrivate','=',true);
                     $results = $query->execute();
                     foreach ($results as $result) {
                         $result->isPrivate = false;
                         $result->save();
                         $changedObjects++;
                     }
                     // Try again with a string ID since on Bullwinkle, the attribute types seem to vary
                     $query = XN_Query::create('Content')->filter('owner')->filter('type','eic',$type)->filter("my->$attr",'=',(string) $object->id)->filter('isPrivate','=',true);
                     $results = $query->execute();
                     foreach ($results as $result) {
                         $result->isPrivate = false;
                         $result->save();
                         $changedObjects++;
                     }
                 }
             }
         }
         
         return ($changedObjects >= self::$objectsPerRequest);
     }
     
     protected static function queryRegular($end) {
         $types = array_merge(self::$exclude, self::$excludeOnVisibility, array_keys(self::$excludeOnLinkedVisibility));
         $query = XN_Query::create('Content')->filter('owner')->begin(0)->end($end)->filter('isPrivate','=',true);
         foreach ($types as $type) {
             $query->filter('type','neic',$type);
         }
         $objects = $query->execute();
         return array('objects' => $objects, 'total' => $query->getTotalCount());
     }
     
     protected static function queryExcludeOnVisibility($end) {
         $query = XN_Query::create('Content')->filter('owner')->begin(0)->end($end)->filter('isPrivate','=',true);
         $filters = array();
         foreach (self::$excludeOnVisibility as $type) {
             $filters[] = XN_Filter('type','eic',$type);
         }
         $query->filter(call_user_func_array(array('XN_Filter','any'), $filters));

         // Only get the visible-to-everyone objects
         $query->filter('my.visibility','=','all');
         $objects = $query->execute();
         return array('objects' => $objects, 'total' => $query->getTotalCount());
     }
     
     protected static function updateConfig() {
        $widget = W_Cache::getWidget('main');
        $widget->config['appPrivacy'] = 'public';
        $widget->saveConfig();
     }
}
