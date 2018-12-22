<?php

W_Cache::getWidget('opensocial')->includeFileOnce('/controllers/EndpointController.php');

/**
 * Controller allowing core access to Person (User) information.
 */
class OpenSocial_PersonController extends OpenSocial_EndpointController {
    
    const MAX_PEOPLE_SIZE = 100;
    
    /**
     * v1.0 Person endpoint.
     *
     * Expected $_GET variables:
     *     'st' => a secure token that when decrypted contains:
     *         'v' => viewer Ning username
     *         'o' => owner Ning username
     *         'd' => domain this request is for
     *         'u' => url this app is served from
     *         'm' => index number for the gadget
     *     'ids' => a comma-delimited list of Ning usernames, empty string for the anonymous user 
     *              or one of (VIEWER, OWNER, VIEWER_FRIENDS, OWNER_FRIENDS)
     *
     * Optional $_GET variables:
     *     'sort' => name or topFriends (this parameter currently has no effect as topFriends is not supported)
     *     'filter' => all, hasApp or topFriends (topFriends currently functions as all as topFriends is not supported)
     *     'first' => offset of first result to return (starts at 0) for paging
     *     'max' => maximum number of results to return, 100 is used if greater than 100 is supplied
     */
    public function action_10_person() {
        $data = $this->initRequest();
        if (! isset($_GET['ids'])) { $this->badRequest(); } 
        $ids =  explode(",", $_GET['ids']);
        $first = ($_GET['first'] ? $_GET['first'] : 0);
        $max = ($_GET['max'] ? $_GET['max'] : 100);
        $max = min(self::MAX_PEOPLE_SIZE, $max);
        W_Cache::current('W_Widget')->includeFileOnce('/lib/helpers/OpenSocial_PersonHelper.php');
        $results = OpenSocial_PersonHelper::getPeople($data->u, $data->v, $data->o, $ids, OpenSocial_PersonHelper::FULL_FORMAT, $_GET['filter'], $_GET['sort'], $first, $max);
        // If we are not returning the full set of results we don't check for invalid ids.  Signaling bad request
        // for an invalid id is not required for OpenSocial 0.7 compliance just recommended so we only do it where
        // another query is not necessary (and thus the performance hit is small).  [Thomas David Baker 2008-07-16]
        // we can't checkIds for hasApp filtering because we are eliminating users legitimately [dkf 2008-09-11]
        if (count($results['people']) < $max && $_GET['filter'] != 'hasApp' && ! OpenSocial_PersonHelper::checkIds($ids, $results['people'])) {
            $this->badRequest();
        }
        $json = new NF_JSON();
        echo $json->encode($results);
    }
}
