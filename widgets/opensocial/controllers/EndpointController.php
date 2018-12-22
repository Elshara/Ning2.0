<?php

/**
 * Superclass for all OpenSocial endpoints.  Contains common initialization and error handling.
 */
abstract class OpenSocial_EndpointController extends W_Controller {
    
    /**
     * Processes the secure token required for all OpenSocial endpoint requests.
     * Returns the appropriate HTTP error code and exits if anything is wrong.
     * Should be called at the beginning of any endpoint request.
     *
     * Expected $_GET variables (even in POST request):
     *     'st' => a secure token that when decrypted contains:
     *         'v' => viewer Ning username
     *         'o' => owner Ning username
     *         'd' => domain this request is for
     *         'u' => url this app is served from
     *         'm' => index number for the gadget
     */
    public function initRequest() {
        header('Content-Type: application/json; charset=UTF-8');
        $json = new NF_JSON();
        // Secure token is always in the query string regardless of HTTP method.
        if (! isset($_GET['st'])) { $this->notAuthorized(); }
        $this->_widget->includeFileOnce('/lib/helpers/OpenSocial_SecurityHelper.php');
        $rawData = OpenSocial_SecurityHelper::decrypt($_GET['st']);
        if (! $rawData) { $this->notAuthorized(); }
        $data = $json->decode($rawData);
        
        // disabled see BAZ-10933 [dkf 2008-10-09]
        //if ($data->d != $_SERVER['HTTP_HOST']) { $this->notAuthorized(); }
        
        if($data->v === NULL) {
            $this->_widget->includeFileOnce('/lib/helpers/OpenSocial_PersonHelper.php');
            $data->v = OpenSocial_PersonHelper::ANONYMOUS;
        }
        return $data;
    }

    /**
     * Sends a 400 error immediately, ending the response.
     */
    public function badRequest() {
        header("HTTP/1.0 400 Bad Request");
        exit();
    }
    
    /**
     * Sends a 401 error immediately, ending the response.
     */ 
    public function notAuthorized() {
        header("HTTP/1.0 401 Not Authorized");
        exit();
    }
    
    /**
     * Sends a 403 error immediately, ending the response.
     */
    public function forbidden() {
        header("HTTP/1.0 403 Forbidden");
        exit();
    }
    
    /**
     * Get the actual screenNames implied by the specified $ids.
     *
     * @param   $viewerId   string  The Ning username of the current viewer or OpenSocial_PersonHelper::ANONYMOUS for the anonymous user.
     * @param   $ownerId    string  The Ning username of the app owner.
     * @param   $ids        array   Array of Ning usernames to retreive data for.
     * @return              array   Array of Ning usernames (possibly including OpenSocial_PersonHelper::ANONYMOUS).  No other values are allowed.
     */
    public static function validIds($viewerId, $ownerId, $ids) {
        return (self::idQueryNeeded($viewerId, $ownerId, $ids) ? self::idQuery($viewerId, $ownerId, $ids) : $ids);
    }
    
    /**
     * Determines if an idQuery is needed to determine the legitimacy of the ids in $ids.
     *
     * @param   $viewerId   string  screenName of the current viewer.
     * @param   $ownerId    string  screenName of the current owner.
     * @param   $ids        string  Array of Ning screenNames.
     * @return              boolean true if idQuery needed.  false if all ids in $ids can be used without checking against the content store.
     */
    public static function idQueryNeeded($viewerId, $ownerId, $ids) {
        foreach ($ids as $id) {
            if ($id !== $viewerId && $id !== $ownerId) {
                return true;
            }
        }
        return false;       
    }

    /**
     * Transform $ids into a list of legitimate screenNames.  This removes ids from the list
     * that are not viewer, owner or friends of the viewer or the owner.  Invalid names are
     * silently dropped.
     *
     * @param   $viewerId   string  screenName of the current viewer.
     * @param   $ownerId    string  screenName of the current owner.
     * @param   $ids        string  Array of Ning screenNames.
     * @return              array   Array of screenNames representing those users from $ids that can legally be queried for app data.
     */
    public static function idQuery($viewerId, $ownerId, $ids) {
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/OpenSocial_PersonHelper.php');
        $results = OpenSocial_PersonHelper::getPeople(null, $viewerId, $ownerId, $ids);
        $users = $results['people'];
        $actualIds = array();
        foreach ($users as $user) {
            $actualIds[] = $user['id'];
        }
        return array_unique($actualIds);
    }
}
