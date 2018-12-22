<?php

// TODO: Rename this class to XG_RequestCache (see Clearspace DOC-2081) [Jon Aquino 2008-09-11]

/**
 * Cache profile and content object loads on a per-request basis
 *
 */
class XG_Cache {

    /** This constant is for invalidating everything in the cache */
    const INVALIDATE_ALL = '#ALL#';
    /** This constant is for saying that max acceptable age is 'forever' */
    const AGE_FOREVER = -1;
    /** This flag can be used to disable query caching **/
    const DISABLE_QUERY_CACHING = 0;

    /** This is controlled by the dontCacheOrderN config variable in the admin
      * widget. Set that config variable to 1 to DISABLE caching of things for
      * which the cache growth will be linear wrt objects added (e.g. detail pages)
      * (BAZ-2969) */
    protected static $_cacheOrderN = null;

    protected static $debug = false;

    /** Debugging and instrumentation */
    public static function allowDebug($debug = true) {
        self::$debug = $debug;
    }

    /** This is very basic and @todo should be spiffed up */
    protected static function printDebug($msg) {
        if (! self::$debug) { return; }
        print '<div style="display: block; border: 1px solid blue">' . xnhtmlentities($msg) . '</div>';
    }

    /**
     * Prepares this class for use.
     */
    public static function initialize() {
        if (XN_Profile::current()->isLoggedIn()) {
            self::$_cache['profiles'][mb_strtolower(XN_Profile::current()->screenName)] = XN_Profile::current();
        }
    }

    /**
    * Recursively converts the given objects, screen names, and email addresses into XN_Profile objects.
    * If content objects are given, their contributorNames are used.
    * Arrays are searched recursively. Empty strings and nulls are ignored.
    *
    * Typically used in action methods to prime the cache using several objects.
    *
    * @param $a, $b, $c, ...  XN_Content objects, XN_Contact objects, XN_Profile objects, screenNames, email addresses, and arrays of the aforementioned.
    * @return  An array of screenName (or email address) => XN_Profile, or if only one item was passed in, a single XN_Profile (or NULL if no profile was found).
    */
    public static function profiles() {
        XG_App::includeFileOnce('/lib/XG_Message.php');
        // TODO: Change "screen name" to "screen name or profile" in code and comment [Jon Aquino 2007-10-09]
        $args = func_get_args();
        $emailAddressesAndScreenNames = User::screenNames($args);
        $screenNames = self::screenNames($emailAddressesAndScreenNames);
        $realEmailAddresses = self::realEmailAddresses($emailAddressesAndScreenNames);
        $pseudoEmailAddresses = self::pseudoEmailAddresses($emailAddressesAndScreenNames);
        $profiles = array();
        // Get error if load mix of screen names and email addresses; so load them separately [Jon Aquino 2007-10-10]
        // avoid array_merge here BAZ-5046 [Phil McCluskey 2007-10-30]
        if ($screenNames) {
            foreach(self::profilesProper($screenNames) as $key => $value) {
                $profiles[$key] = $value;
            }
        }
        if ($realEmailAddresses) {
            foreach(self::profilesProper($realEmailAddresses) as $key => $value) {
                $profiles[$key] = $value;
            }
        }
        if ($pseudoEmailAddresses) {
            foreach(self::profilesProper($pseudoEmailAddresses) as $key => $value) {
                $profiles[XG_Message::pseudoEmailAddress($key)] = $value;
            }
        }
        if (count($args) == 1 && ! is_array(reset($args))) {
            return count($profiles) ? reset($profiles) : NULL;
        }
        // Performance optimization: automatically prime the User cache, if more than one profile is requested [Jon Aquino 2007-09-20]
        if (count($profiles) > 1) { User::loadMultiple($profiles); }
        return $profiles;
    }

  /**
   * Filters out everything but screen names.
   *
   * @param $emailAddressesAndScreenNames array  a list of email addresses and usernames
   * @return array  the usernames (with keys same as values)
   */
  private static function screenNames($emailAddressesAndScreenNames) {
      $results = array();
      foreach ($emailAddressesAndScreenNames as $x) {
          if (strpos($x, '@') === false) { $results[$x] = $x; }
      }
      return $results;
  }

  /**
   * Filters out everything but email addresses that do not end with "@users".
   *
   * @param $emailAddressesAndScreenNames array  a list of email addresses and usernames
   * @return array  the email addresses (with keys same as values)
   * @see pseudoEmailAddresses()
   */
  private static function realEmailAddresses($emailAddressesAndScreenNames) {
      XG_App::includeFileOnce('/lib/XG_Message.php');
      $results = array();
      foreach ($emailAddressesAndScreenNames as $x) {
          if (strpos($x, '@') !== false && ! XG_Message::isPseudoEmailAddress($x)) { $results[$x] = $x; }
      }
      return $results;
  }

  /**
   * Filters out everything but email addresses of the form "screenname@users".
   * The messaging core understands this pseudo-email-address, but XN_Profile queries do not.
   *
   * @param $emailAddressesAndScreenNames array  a list of email addresses and usernames
   * @return array  the screen names (with keys same as values)
   */
  private static function pseudoEmailAddresses($emailAddressesAndScreenNames) {
      XG_App::includeFileOnce('/lib/XG_Message.php');
      $results = array();
      foreach ($emailAddressesAndScreenNames as $x) {
          if (strpos($x, '@') !== false && XG_Message::isPseudoEmailAddress($x)) {
              $screenName = mb_substr($x, 0, -6);
              $results[$screenName] = $screenName;
          }
      }
      return $results;
  }

  protected static $_cache = array();

  /**
   * IDs that were tried but failed to retrieve data. Keyed by type and ID.
   */
  protected static $_invalidIds = array();

  private static function get($class, $ids) {
      if (! is_array($ids)) {
          $returnScalar = true;
          $ids = array($ids);
      } else {
          $returnScalar = false;
      }

      $keyCallback = array('self', "key_$class");
      if (! is_callable($keyCallback)) {
          throw new Exception("No key lookup callback for $class");
      }
      $r = array();
      $toRetrieve = array();
      foreach ($ids as $i => $id) {
          $idToLookup = call_user_func($keyCallback, $id);
          if (isset(self::$_cache[$class][$idToLookup])) {
              $r[$i] = self::$_cache[$class][$idToLookup];
          } elseif (isset(self::$_invalidIds[$class][$idToLookup])) {
              // Skip it [Jon Aquino 2008-01-02]
          } else {
              $toRetrieve[$idToLookup] = $i;
          }
      }

      if (count($toRetrieve)) {
          $callback = array('self', 'get_' . $class);
          if (! is_callable($callback)) {
              throw new Exception("No cache retrieval callback for $class");
          }
          $retrieved = call_user_func($callback, $toRetrieve);
          foreach ($retrieved['objects'] as $object) {
              $idOfRetrievedObject = $object->{$retrieved['idProperty']};
              $toRetrieveKey = call_user_func($keyCallback, $idOfRetrievedObject);
              $keyInReturnedArray = $toRetrieve[$toRetrieveKey];
              if ($class == 'profiles') {
                  $r[$keyInReturnedArray] = self::$_cache[$class][mb_strtolower($object->email)] = self::$_cache[$class][mb_strtolower($object->screenName)] = $object;
              } else {
                  $r[$keyInReturnedArray] = self::$_cache[$class][$toRetrieveKey] = $object;
              }
          }
      }
      foreach ($ids as $i => $id) {
        $idToLookup = call_user_func($keyCallback, $id);
          if (! array_key_exists($i, $r)) { self::$_invalidIds[$class][$idToLookup] = $idToLookup; }
      }
      if ($returnScalar) {
          return $r[0];
      } else {
          return $r;
      }
  }

  private static function key_content($id) { return $id; }
  private static function get_content($toRetrieve) {
      if (count($toRetrieve) == 1) {
          // Prefer XN_Content::load($id) to XN_Content::load($ids), as the former is guaranteed
          // to return the object immediately, even when the object is created on another computer.
          // This is important for Videos - the VideoAttachment object is created by a different
          // computer (the transcoder). See BAZ-803. [Jon Aquino 2006-12-18]
          $ids = array_keys($toRetrieve);
          $objects = array(XN_Content::load($ids[0]));
      } else {
          $objects = XN_Content::load(array_keys($toRetrieve));
      }
      return array('objects' => $objects, 'idProperty' => 'id');
  }

  private static function key_profiles($screenName) { return mb_strtolower($screenName); }
  private static function get_profiles($toRetrieve) {
      $idsToRetrieve = array_keys($toRetrieve);
      if (count($idsToRetrieve) == 1) {
          try {
              $objects = array(XN_Profile::load($idsToRetrieve[0]));
          } catch (Exception $e) {
              // Get here if profile not found  [Jon Aquino 2007-01-18]
              $objects = array();
          }
      } else {
          $objects = XN_Profile::load($idsToRetrieve);
      }
      return array('objects' => $objects, 'idProperty' => $idsToRetrieve && mb_strpos(reset($idsToRetrieve), '@') !== false ? 'email' : 'screenName');
  }

  /** Methods for retrieving data from the core */
  public static function content($ids) { return self::get('content', $ids); }
  private static function profilesProper($ids) { return self::get('profiles', $ids); }

  /**
   * Generates a cache invalidation key for app-wide use based on its argument(s)
   *
   *   When passed this.....                    Returns this....[1]
   * --------------------------------------------------------------------------
   *   XN_Profile object                        xg-user-<screenName>
   *   User Content object                      xg-user-<content contributor screenName>
   *   'user', screen name as string            xg-user-<screenName>
   *   Other content object                     xg-content-<id>
   *   'promotion', content object              xg-promotion-<content object type>
   *   'moderation', XN_Profile or User object  xg-moderation-<screenName>
   *   'moderation', XN_Profile or User object,
   *       content object                       xg-moderation-<screenName>-<content object type>
   *   'moderation', XN_Profile or User object
   *       or XN_Application object or string [3],
   *       W_Widget object                      xg-moderation-<screenName->-w-<widget instance name>
   *   'type', content object                   xg-type-<content object type>
   *   'type', string                           xg-type-<string>
   *   XG_Embed object                          xg-embed-<embed locator>-<[2]>
   *
   * When content object types or screen names are incorporated into cache keys,
   * they are made all-lowercase first.
   *
   * [1] The portion of the generated cache invalidation key after the initial
   * 'xg-' is hashed with the XG_Cache::hash() function before it is returned
   * to save space.
   *
   * [2] The second part of the embed cache key is 'o' if the embed object is
   * owned by the current user and 'u' otherwise.
   *
   * [3] If an XN_Application object is provided here, the screen name of the
   * application owner is used for the <screenName> portion of the key. If a
   * string is provided here, the string is used for the <screenName> portion
   * of the key.
   *
   *
   * The cache keys begin with "xg-" to prevent overlap with user-created keys
   * and with widget-specific keys {@see widgetKey()}.
   *
   * @return string
   */
  public static function key() {
      $args = func_get_args();
      $argc = func_num_args();
      // XN_Profile -> xg-user-<screenName>
      if (($argc == 1) && ($args[0] instanceof XN_Profile)) {
          $key = 'user-' . mb_strtolower($args[0]->screenName);
      }
      // User object -> xg-user-<content contributor screenName>
      else if (($argc == 1) && self::isContent($args[0]) && ($args[0]->type == 'User')) {
          $key = 'user-' . mb_strtolower($args[0]->contributorName);
      }
      // user', screen name as string -> xg-user-<screenName>
      else if (($argc == 2) && (strcasecmp('user', $args[0]) == 0)) {
          $key = 'user-' . mb_strtolower((string) $args[1]);
      }
      // Other content object -> xg-content-<id>
      else if (($argc == 1) && self::isContent($args[0])) {
          $key = 'content-' . $args[0]->id;
      }
      // 'promotion', content object => xg-promotion-<content object type>
      else if (($argc == 2) && (strcasecmp('promotion', $args[0]) == 0) && self::isContent($args[1])) {
          $key = 'promotion-' . mb_strtolower($args[1]->type);
      }
      // 'moderation', XN_Profile object or User object -> xg-moderation-<screenName>
      else if (($argc == 2) && (strcasecmp('moderation', $args[0]) == 0)) {
          if ($args[1] instanceof XN_Profile) {
              $key = 'moderation-' . mb_strtolower($args[1]->screenName);
          }
          else if (self::isContent($args[1]) && ($args[1]->type == 'User')) {
              $key = 'moderation-' . mb_strtolower($args[1]->contributorName);
          }
          else {
              throw new Exception("XG_Cache::key() doesn't know how to handle 'moderation', {$args[1]}");
          }
      }
      // 'moderation', XN_Profile, content object -> xg-moderation-<screenName>-<content object type>
      else if (($argc == 3) && (strcasecmp('moderation', $args[0]) == 0) && self::isContent($args[2])) {
          if ($args[1] instanceof XN_Profile) {
              $key = 'moderation-' . mb_strtolower($args[1]->screenName) . '-' . mb_strtolower($args[2]->type);
          } else if (self::isContent($args[1]) && ($args[1]->type == 'User')) {
              $key = 'moderation-' . mb_strtolower($args[1]->contributorName) . '-' . mb_strtolower($args[2]->type);
          } else {
              throw new Exception("XG_Cache::key() doesn't know how to handle 'moderation',{$args[1]},{$args[2]->id}");
          }
      }
      // 'moderation', XN_Profile or User object or XN_Application object,
      //       or XN_Application object or string [3],
      //       W_Widget object                      xg-moderation-<screenName->-m-<widget instance name>
      else if (($argc == 3) && (strcasecmp('moderation', $args[0]) == 0) && ($args[2] instanceof W_BaseWidget)) {
          if ($args[1] instanceof XN_Profile) {
              $key = 'moderation-' . mb_strtolower($args[1]->screenName) . '-w-' . mb_strtolower($args[2]->dir);
          } else if (self::isContent($args[1]) && ($args[1]->type == 'User')) {
              $key = 'moderation-' . mb_strtolower($args[1]->contributorName) . '-w-' . mb_strtolower($args[2]->dir);
          } else if (($args[1] instanceof XN_Application)) {
              $key = 'moderation-' . mb_strtolower($args[1]->ownerName) . '-w-' . mb_strtolower($args[2]->dir);
          } else if (is_string($args[1])) {
              $key = 'moderation-' . mb_strtolower($args[1]) . '-w-' . mb_strtolower($args[2]->dir);
          } else {
              throw new Exception("XG_Cache::key() doesn't know how to handle 'moderation',{$args[1]},{$args[2]->dir}");
          }
      }
      // 'type', content object                   xg-type-<content object type>
      // 'type', string                           xg-type-<string>
      else if (($argc == 2) && (strcasecmp('type', $args[0]) == 0)) {
          if (self::isContent($args[1])) {
              $type = $args[1]->type;
          } else if (is_string($args[1]) || is_numeric($args[1])) {
              $type = $args[1];
          }
          $key = 'type-' . mb_strtolower($type);
      }
      // XG_Embed object                          xg-embed-<embed locator>-<[2]>
      else if (($argc == 1) && ($args[0] instanceof XG_Embed)) {
          $key = 'embed-' . $args[0]->getLocator() . '-'. ($args[0]->isOwnedByCurrentUser() ? 'o' : 'u');
      } else {
          throw new Exception("Invalid set of arguments passed to XG_Cache::key()");
      }

      XG_Cache::printDebug("About to return xg-hash($key)");
      return 'xg-' . self::hash($key);
  }

  private static function isContent($o) { return (($o instanceof XN_Content) || ($o instanceof W_Content)); }

  /**
   * Generates a cache invalidation key for a particular widget's use based on
   * its argument(s)
   *
   *   When passed this.....                    Returns this....[1]
   * --------------------------------------------------------------------------
   *   W_Widget object, string                  xg:<widget instance name>-<string>
   *   widget instance name as string, string   xg:<widget instance name>-<string>
   *   string                                   xg:<current widget instance name>-<string>
   *
   * [1] The portion of the generated cache invalidation key after the initial
   * 'xg:' is hashed with the XG_Cache::hash() function before it is returned
   * to save space.
   *
   * The cache keys begin with "xg:" to prevent overlap with user-created keys
   * and with app-wide keys. {@see key()}.
   *
   * @return string
   */
   public static function widgetKey() {
       $args = func_get_args();
       $argc = func_num_args();
       if ($argc == 1) {
           $instanceName = W_Cache::current('W_Widget')->dir;
           $str = (string) $args[0];
       }
       else if ($argc == 2) {
           if ($args[0] instanceof W_BaseWidget) {
               $instanceName = $args[0]->dir;
           } else {
               $instanceName = (string) $args[0];
           }
           $str = (string) $args[1];
       }
       else {
           throw new Exception("XG_Cache::widgetKey() doesn't know what to do with $argc arguments");
       }

       return 'xg:' . self::hash("$instanceName-$str");
   }

   /** Methods and properties for managing the persistent cache
    * This is a file based cache that other code can use for storing
    * data between requests
    */

   /** Default max age if not specified */
   public static $maxAge = 86400;

   /** Max size of the persistent cache, in files */
   protected static $maxCacheSize = 2000;
   public static function setMaxCacheSize($i) { self::$maxCacheSize = $i; }
   public static function getMaxCacheSize() { return self::$maxCacheSize; }

   /** Percent of time, on writing to the cache, that the cache will be cleaned
    * of too many files */
   protected static $cacheCleanupPercentage = 1;
   public static function setCacheCleanupPercentage($i) {
       $i = (integer) $i;
       if (($i < 0) || ($i > 100)) { throw new XN_IllegalArgumentException("Cache cleanup percentage $i is not between 0 and 100"); }
       self::$cacheCleanupPercentage = $i;
   }
   public static function getCacheCleanupPercentage() { return self::$cacheCleanupPercentage; }


   /** Percent of time, when reading from the cache (whether it's a hit or miss)
     * that too-old files with the same cache ID will be cleaned up. Note that the
     * "too-old" ness is relative to the specified max age in a particular call
     * to load()
     */
   protected static $tooOldCleanupPercentage = 25;
   public static function setTooOldCleanupPercentage($i) {
       $i = (integer) $i;
       if (($i < 0) || ($i > 100)) { throw new XN_IllegalArgumentException("Too old cleanup percentage $i is not between 0 and 100"); }
       self::$tooOldCleanupPercentage = $i;
   }
   public static function getTooOldCleanupPercentage() { return self::$tooOldCleanupPercentage; }

   /**
    * Retrieve a piece of data from the persistent cache
    *
    * @param $cacheId string The identifier of the piece of data in the persistent cache
    * @param $maxAge integer optional maximum age in seconds for a piece of stored data to be valid
    * @param $options array Array of options that alter behavior:
    *     'returnContent': Whether to return the content or just test for presence.
    *                      If true (or missing -- true is the default) the method
    *                      returns the cached data if present or an XG_Cache_Miss
    *                      object if not. If false, the method returns true if the
    *                      cached data is present or false if not
    *     'keys': array of cache keys to attach as invalidation conditions. A call that
    *                      specifies invalidation keys that are not already present on
    *                      an otherwise-matching cache entry uses a new cache entry.
    *
    * @return mixed
    */
    public static function load($cacheId, $maxAge = null, $options = array()) {
        // Prepare options with correct defaults
        if (! is_array($options)) { $options = array(); }
        if (! isset($maxAge)) { $maxAge = self::$maxAge; }
        if (isset($options['keys'])) {
            if (! is_array($options['keys'])) { $options['keys'] = array($options['keys']); }
        } else {
            $options['keys'] = array();
        }
        if (! isset($options['returnContent'])) { $options['returnContent'] = true; }

        if (self::DISABLE_QUERY_CACHING) { return ($options['returnContent'] ? (new XG_Cache_Miss) : false); }

        // Build a correct glob that includes the cacheId and all of the invalidation
        // keys
        $glob = self::buildCacheGlob($cacheId);
        if (count($options['keys']) > 0) {
            sort($options['keys']);
            // add each key
            foreach ($options['keys'] as $key) {
                $glob .= '*#' . $key . '#*';
            }
            $glob = str_replace('**','*', $glob);
        }

       self::printDebug("Glob for $cacheId: $glob");
       $files = glob($glob);
       self::printDebug("Found files: " . implode(', ', $files));
       $fileCount = count($files);
       // If no files match, then the test fails.
       if ($fileCount == 0) {
           self::writeStatistics('MISS', $cacheId);
           return ($options['returnContent'] ? (new XG_Cache_Miss) : false);
       }
       // Multiple cache IDs may hash to the same value, so what we need to return
       // here (if there's a hit) is the newest file that contains a matching
       // cache ID.

       // If more than one file matches, use the newest that matches (and remove the rest)
       if (count($files) > 1) {
           // Since the first part of the cache filename is its creation time,
           // sorting the filenames sorts them by creation time. (Thanks, Dan & NF_Controller!)
           rsort($files);
       }

       // Assume it's a miss, the loop will reset it if not
       $retval = null; $found = false; $now = time();
       for ($i = 0, $j = count($files); $i < $j; $i++) {
           // Check file age first, and don't bother looking in the file if
           // it is too old
           if ($maxAge != self::AGE_FOREVER) {
               $creationTime = self::getCreationTimeFromCacheFilename($files[$i]);
               if ($creationTime === false) {
                   throw new Exception("Invalid cache filename: {$files[$i]}");
               }
               // The file is too old if its age is > maxAge
               if (($now - $creationTime) > $maxAge) {
                   // Dont bother looking for any more files, since as soon as we
                   // encounter one that's too old, the rest will be too old, too,
                   // since $files is sorted newest-first (BAZ-2186)
                   self::printDebug("File $i ({$files[$i]}) is too old ($now - $creationTime > $maxAge), skipping the rest of $j files");
                   break;
               }
           }

           // If we're here, we've found a new-enough file, let's make sure
           // the ID matches

           // Make sure the cache ID of the file matches the provided cache ID
           $contents = self::getContentsForCacheId($files[$i], $cacheId);
           if ($contents instanceof XG_Cache_Miss) {
               self::printDebug("Skipping #$i ({$files[$i]}): embedded ID doesn't match");
               continue;
           } else {
               self::printDebug("Setting found for #$i: {$files[$i]}");
               $found = true;
               $retval = $contents;
               break;
           }
       }

       if ($found) {
           self::printDebug("Found data for $cacheId in #$i: $files[$i]");
           if (self::getGatherStatistics()) {
               // The filesize() call could be expensive, so only do it if we're
               // gathering stats (or is it in the statcache anyway?)
               self::writeStatistics('HIT', $cacheId, $files[$i], filesize($files[$i]));
           }
           // If we're just returning whether we found something, set $retval
           // appropriately
           if (! $options['returnContent']) {
               $retval = true;
           }
       } else {
           self::writeStatistics('MISS', $cacheId);
           $retval = $options['returnContent'] ? (new XG_Cache_Miss) : false;
       }

       // Some of the time, Delete any older files with the same cache ID
       // We do this whether it was a hit or a miss (BAZ-2186)
       if (mt_rand(1, 100) <= self::getTooOldCleanupPercentage()) {
           $filesToDelete = array();
           // If we actually found valid, results, advance past the file we're returning
           if ($found) {
               $i++;
           }
           while ($i < $j) {
               self::printDebug("Testing #$i: {$files[$i]} for cache ID $cacheId");
               if (! (self::testContentsForCacheId($files[$i], $cacheId) instanceof XG_Cache_Miss)) {
                   self::printDebug("Want to delete #$i: {$files[$i]} for cache ID $cacheId");
                   $filesToDelete[] = $files[$i];
               }
               $i++;
           }
           self::deleteCacheFiles($filesToDelete);
       }
       return $retval;
   }

   /**
    * Put a piece of data into the persistent cache
    *
    * @param $cacheId string
    * @param $data mixed
    * @param $options array controlling saving behavior
    *    'keys': array of cache keys to attach as invalidation conditions
    * @return boolean true if save succeeds
    */
   public static function save($cacheId, $data, $options = null) {
       if (self::DISABLE_QUERY_CACHING) { return true; }
       $keys = null;
       if (is_array($options)) {
           if (isset($options['keys'])) { $keys = $options['keys']; }
       }
       $filename = self::baseCachePath() . '/' . self::buildCacheFilename($cacheId, $keys);
       self::printDebug("Saving $cacheId in $filename");
       // The (unhashed) cache ID is put into the cache file along with the data
       // so that load() can disambiguate between cache IDs that hash to the same value
       // The actual data to store is double-serialized to prevent memory leaks
       // when serializing arrays of content objects (BAZ-2186)
       $dataToWrite = serialize(array((string) $cacheId, serialize($data)));
       self::writeStatistics('WRITE',$cacheId, $filename, strlen($dataToWrite));
       @mkdir(self::baseCachePath());
       $tempFilename = tempnam(self::baseCachePath(), '.tmp');
       file_put_contents($tempFilename, $dataToWrite);
       @rename($tempFilename, $filename);
       if (mt_rand(1, 100) <= self::getCacheCleanupPercentage()) {
           self::cleanupCache();
       }
       return true;
   }

   /**
    * Test if a piece of data is in the persistent cache
    *
    * @param $cacheId string
    * @param $maxAge integer optional maximum age in seconds for a piece of stored data to be valid
    * @return boolean true if the data is there, false if not
    */
   public static function test($cacheId, $maxAge = null) {
       if (self::DISABLE_QUERY_CACHING) { return false; }
       return self::load($cacheId, $maxAge, array('returnContent' => false));
   }

   /**
    * Remove a piece of data from the persistent cache
    *
    * @param $cacheId string
    */
    public static function remove($cacheId) {
        $glob = self::buildCacheGlob($cacheId);
        $files = glob($glob);
        self::deleteCacheFiles($files);
    }

   /**
    * Invalidate everything in the persistent cache with a given key (or keys)
    *
    * @param string|array One or an array of invalidation keys
    */
    public static function invalidate($keys) {
        if (! is_array($keys)) { $keys = array($keys); }
        foreach ($keys as $key) {
            if ($key == XG_Cache::INVALIDATE_ALL) {
                $files = glob(self::baseCachePath() . '/*');
            } else {
                $files = glob(self::baseCachePath() . '/*#' . $key . '#*');
            }
            self::deleteCacheFiles($files);
        }
    }

   /**
    * Cache filenames are of the form cacheId_creationTime=#key1#key2#...#keyn#
    *
    * @param $cacheId string
    * @param $keys string|array optional string of 1 cache key or array of multiple cache keys
    * @return string
    */
   protected static function buildCacheFilename($cacheId, $keys = null) {
       // Put the creation time first so that sorting a globbed list of filenames
       // sorts them by creation time
       $cacheFilename = self::baseConvert(time(), 10, 62) .'=';
       // Add a perturbation factor in case keys are provided in the same
       // second that have colliding hash values
       $now = microtime(true);
       // 9999 is three digits (2Bh) in Base 62, so zero-pad the perturbation
       // factor for proper sorting (BAZ-2186)
       $now = sprintf('%03s',self::baseConvert(intval(10000 * ($now - floor($now))), 10, 62));
       $cacheFilename .= $now .'=';
       // Then comes the cache ID
       $cacheFilename .= self::hash($cacheId) . '_';
       if (isset($keys)) {
           // Handle scalars as 1-element arrays
           if (! is_array($keys)) { $keys = array($keys); }
           // Order of key specification doesn't matter
           sort($keys);
           foreach ($keys as $key) {
               if (preg_match('@[#^=/]@u', $key)) {
                   throw new Exception("Invalidation keys cannot contain # ^ = /");
               }
               $cacheFilename .= '#' . $key .'#';
           }
       }
       if (($cfl = strlen($cacheFilename)) > 256) {
           if (is_array($keys)) {
               $keyMsg = ' and keys ' . implode(', ' , $keys);
               throw new Exception("Cache filename length of $cfl too long (> 256 bytes) for cache ID $cacheId$keyMsg");
           }
       }
       return $cacheFilename;
   }

   /**
    * Build a glob to match everything with a given cache ID
    *
    * @param $cacheId string
    * @return string
    */
    public static function buildCacheGlob($cacheId) {
        return self::baseCachePath() . '/*=*=' . self::hash($cacheId) . '_*';
    }

   protected static function baseCachePath() {
       return $_SERVER['DOCUMENT_ROOT'] . '/xn_private/xn_volatile/xg-cache';
   }

   protected static function getCreationTimeFromCacheFilename($filename) {
       if (preg_match('@(?:^|.*/)([0-9a-zA-Z]+)=[0-9a-zA-Z]+=[0-9a-zA-Z]+_[^/]*$@u', $filename, $matches)) {
           return self::baseConvert($matches[1], 62, 10);
       } else {
           return false;
       }
   }

   /**
    * Remove the oldest files from the cache if there are more than
    * self::$maxCacheSize files
    */
   public static function cleanupCache() {
       $files = glob(self::baseCachePath().'/*');
        //  The first part of the filename is a base 36 encoded expiration
        //    timestamp, so sorting alphabetically also sorts by
        //    expiration date!
        sort($files);
        $numToRemove = count($files) - self::getMaxCacheSize();
        if ($numToRemove > 0) {
            $filesToRemove = array_slice($files, 0, $numToRemove);
            self::deleteCacheFiles($filesToRemove);
        }
   }

   protected static function deleteCacheFiles($filenames) {
       if (! is_array($filenames)) { $filenames = array($filenames); }
       foreach ($filenames as $filename) {
           if (strpos($filename, self::baseCachePath() . '/') === 0) {
               self::printDebug("Removing $filename");
               self::writeStatistics('DELETE', $filename);
               @unlink($filename);
           } else {
               throw new Exception("Can't delete $filename: not under cache path");
           }
       }
   }

   /**
    * Unserialize and return the contents of $filename if the stored
    * cache ID matches $cacheId
    *
    * @param $filename string File to open
    * @param $cacheId string Cache ID
    * @return mixed|XG_Cache_Miss Returns the contents if the cache IDs match
    * or an XG_Cache_Miss object if they don't
    */
   protected static function getContentsForCacheId($filename, $cacheId) {
       if (self::DISABLE_QUERY_CACHING) { return new XG_Cache_Miss; }
       self::printDebug("Looking for $cacheId in $filename");
       $contents = @file_get_contents($filename);
       if ($contents === false) { return new XG_Cache_Miss; }
       $data = @unserialize($contents);
       self::printDebug("In $filename, found {$data[0]}->{$data[1]}");
       if (is_array($data) && isset($data[0]) && isset($data[1]) && ($data[0] === ((string) $cacheId))) {
           // The IDs matched, so unserialize the matched data
           $dataToReturn = unserialize($data[1]);
           self::printDebug("Returning $dataToReturn");
           return $dataToReturn;
       } else {
           self::printDebug("Returning miss");
           return new XG_Cache_Miss;
       }
   }

   protected static function testContentsForCacheId($filename, $cacheId) {
       if (self::DISABLE_QUERY_CACHING) { return new XG_Cache_Miss; }
       self::printDebug("Testing for $cacheId in $filename");
       $contents = @file_get_contents($filename);
       if ($contents === false) { return new XG_Cache_Miss; }
       $data = @unserialize($contents);
       self::printDebug("Testing $filename, found {$data[0]}->{$data[1]}");
       return (is_array($data) && isset($data[0]) && isset($data[1]) && ($data[0] === ((string) $cacheId)));
   }

   /** Utility methods for data manipulation */

   /**
    * A hash function to shrink strings so they take up less space in the
    * cache key. Using the first ten characters (40 bits) of an MD5 hash,
    * assuming the
    * hash space is evenly distributed, should cause a collision only in
    * 1 out of every 16^10 = 2^40 = 1,099,511,627,776 hashes.
    *
    * @param $str string What to hash
    * @return string The hashed value
    */
   public static function hash($str) {
       return self::baseConvert(substr(md5($str), 0, 10), 16, 62);
   }

   /**
    * The built-in base_convert() function can only handle up to base 36 and loses
    * precision when faced with values on the large-side of md5 hashes. This method
    * goes up to base 62 and does not have any precision loss problems. It is, however,
    * approximately 59 times slower than the built-in base_convert() function. In
    * absolute terms, though, that is a difference of about 0.00075 seconds per iteration
    * compared to 0.04467 seconds, so it's not too much of a concern.
    *
    * @param $in string The value to convert
    * @param $in_base string The base of $in
    * @param $out_base string The base to convert $in to
    * @return string
    */
   public static function baseConvert($in, $in_base, $out_base) {
       if ($in_base <= 36) {
           $g_in = gmp_init($in, $in_base);
       } else {
           $g_in = gmp_init(0);
           $digits = strlen($in) - 1;
           for ($place = $digits; $place >= 0; $place-- ) {
               $place_value = strpos(self::$baseConversionCharacters, $in[$place]);
               if ($place_value === false) {
                   throw new Exception("Unknown character ({$in[$place]}) at position $place of $in");
               }
               // A digit in place X is worth base^X * (value of digit)
               $g_in = gmp_add($g_in, gmp_mul(gmp_pow($in_base, $digits - $place), $place_value));
           }
       }
       XG_Cache::printDebug("converting $in (" . gmp_strval($g_in) . ") to base $out_base");
       // Handle 0 properly (BAZ-1565)
       if (gmp_strval($g_in) === '0') {
           return '0';
       }
       $out = '';
       while (gmp_cmp($g_in, 0) > 0) {
           list ($g_in, $g_rem) = gmp_div_qr($g_in, $out_base);
           $out = self::$baseConversionCharacters[gmp_intval($g_rem)] . $out;
       }
       return $out;
   }

   /**
    * The characters that baseConvert() uses for each value. The character at
    * position X in this string is the character that represents a place value
    * of X in numbers whose base is >= X.
    */
    public static $baseConversionCharacters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    /**
     * Methods for diagnostics and getting cache information
     */

     /**
      * How many files are in the cache right now?
      *
      * @return integer
      */
     public static function getCacheSize() {
         return count(glob(self::baseCachePath() . '/*'));
     }

     protected static $gatherStatistics = false;
     public static function setGatherStatistics($g) { self::$gatherStatistics = (boolean) $g; }
     public static function getGatherStatistics() { return self::$gatherStatistics; }

     /**
      * Get the saved statistics
      *
      * @return array Each line is a tab-delimited list of info
      */
     public static function getStatistics() {
         $statistics = array();
         foreach (glob(self::getStatisticsFilePath('*')) as $file) {
             $statistics = array_merge($statistics,file($file));
         }
         ksort($statistics);
         return $statistics;
     }

     /**
      * Clear all stats files
      *
      */
     public static function clearStatistics() {
         foreach (glob(self::getStatisticsFilePath('*')) as $file) {
             @unlink($file);
         }
     }

     protected static function baseStatisticsPath() {
         return $_SERVER['DOCUMENT_ROOT'] . '/xn_private/xn_volatile/xg-cache-stats';
     }

     protected static function getStatisticsFilePath($numberOrStar) {
         if (! preg_match('/^[0-9]+|\*$/u', $numberOrStar)) {
             throw new XN_IllegalArgumentException("$numberOrStar is not * or a number");
         }
         return self::baseStatisticsPath() . '/stats.' . $numberOrStar;
     }

     protected static function writeStatistics() {
         if (! self::getGatherStatistics()) { return; }
         $args = func_get_args();
         $info = implode("\t", $args);
         $file = self::getStatisticsFilePath(getmypid());
         @mkdir(self::baseStatisticsPath());
         $fp = @fopen($file, 'a');
         if (! $fp) {
             throw new Exception("Can't open XG_Cache statistics file: $file");
         }
         $res = fwrite($fp, microtime(true) . "\t" . $info . "\n");
         if ($res === false) {
             throw new Exception("Can't write XG_Cache statistics data to $file");
         }
         $res = fclose($fp);
         if ($res === false) {
             throw new Exception("Can't close XG_Cache statistics file: $file");
         }
     }

    /**
     * Cache things which grow linearly with content growth (like detail pages)?
     *   (BAZ-2969)
     */
    public static function cacheOrderN() {
        if (!isset(self::$_cacheOrderN)) {
            $adminWidget = W_Cache::getWidget('admin');
            self::$_cacheOrderN = $adminWidget->config['dontCacheOrderN'] ? false : true;
        }
        return self::$_cacheOrderN;
    }

    /**
     * Returns whether cached output with the given ID is available and hasn't yet expired.
     * If so, outputs the cached output; otherwise, captures the output until the matching
     * outputCacheEnd() call. Usage is similar to the Cache_Lite_Output functions:
     *
     *     if (! XG_Cache::outputCacheStart('feed', 3600)) {
     *         echo 'Expensive output here';
     *         XG_Cache::outputCacheEnd('feed');
     *     }
     *
     * One advantage of this technique over standard action caching is that the expensive
     * cache-building is done in the first request only; other requests continue to use the old cache until
     * the new cache is built.
     *
     * @param $id string  ID for the cache entry
     * @param $maxAge integer  max age in seconds
     * @return boolean  whether valid cached output is available
     */
    public static function outputCacheStart($id, $maxAge) {
        list($then, $data) = XN_Cache::get($id);
        if ($then < time() - 2*$maxAge) {
            try {
                XN_Cache::remove($id . '-lock'); // Just in case [Jon Aquino 2007-09-04]
            } catch(Exception $e) { }
        }
        if ($then < time() - $maxAge && XN_Cache::insert($id . '-lock', 'foo')) {
            ob_start();
            return false;
        }
        echo $data;
        return true;
    }

    /**
     * Finishes the output capture started by outputCacheStart().
     *
     * @param $id string  ID for the cache entry
     */
    public static function outputCacheEnd($id) {
        $data = ob_get_contents();
        ob_end_clean();
        XN_Cache::put($id, array(time(), $data));
        XN_Cache::remove($id . '-lock');
        echo $data;
    }

    /**
     * Attempts to lock the specified id exclusively for the caller for the
     * duration specified (in seconds).  If the lock exists this will return
     * false.  If the lock is created it will return true.
     *
     * @param 	$id			string		ID to lock.  Expected to be of the form
     *							        "type-name-" . md5("unique, attributes")
	 * @param   $duration   int   		Time to hold the lock for in seconds.
	 * @param	$cacher		object		Class to use as the cacher. Test purposes only.
	 * @return 	boolean		true for successful lock, false if lock not possible.
     * @deprecated  Use XG_LockHelper instead. But note that $duration is not the same as $waitTimeout.
     */
    public static function lock($id, $duration = 120, $cacher = NULL) {
		if (!$cacher) {
			$cacher = new XN_Cache;
		}
		if ($cacher->insert($id, time() + $duration)) {
			return true;
		}
		if ($then = $cacher->get($id)) { // the key already exists
			if ($then > time()) { // duration is not expired
				return false;
			}
			// We cannot just remove the key, another process could already replace it with the new one.
			XG_App::includeFileOnce('/lib/XG_LockHelper.php');
			$repairKey = "$id-XG_Cache-repair-32409gu4095";
			if (!XG_LockHelper::lock($repairKey, 0)) {
				return false;
			}
			// Read it again to check that we still work with the same key
			if ($cacher->get($id) != $then) {
				XG_LockHelper::unlock($repairKey);
				return false;
			}
			// Ok, we can proceed with the deletion.
			try { $cacher->remove($id); } catch(Exception $e) { }
			XG_LockHelper::unlock($repairKey);
		} else { // the key just disappeared? try to insert it once again
			// do nothing
		}
		// the last chance to obtain the key
		return $cacher->insert($id, time() + $duration);
    }

    /**
     * Explicitly remove the lock on the specified id.
     *
     * @param	$id		ID to unlock.  Expected to be of the form
     *						"type-name-" . md5("unique, attributes")
     * @return 	boolean	true if there is no longer a lock on the specified $id,
     *					false if a lock remains.  NOTE a return value of true
     *					does not imply that there was a lock to remove, just that
     *					no lock exists now.
     */
    public static function unlock($id) {
        XN_Cache::remove($id);
        return (! XN_Cache::get($id));
    }
}

XG_Cache::initialize();

// A class to represent a special return type from XG_Cache::load() that indicates
// nothing matching was in the cache
class XG_Cache_Miss { }
