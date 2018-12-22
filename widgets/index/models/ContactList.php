<?php

/**
 * A temporary list of contacts, used when sending invitations.
 */
class ContactList extends W_Model {

    /**
     * Serialized array of contacts, each being an array with keys "name" and "emailAddress"
     *
     * @var XN_Attribute::STRING
     */
    public $contacts;

    /**
     * The mozzle that created this object (always "main")
     *
     * @var XN_Attribute::STRING
     */
   public $mozzle;

    /**
     * System attribute marking whether to make the content available on Ning search results.
     *
     * @var XN_Attribute::STRING
     */
    public $isPrivate;

/** xn-ignore-start 2365cb7691764f05894c2de6698b7da0 **/
// Everything other than instance variables goes below here

    /**
     * Creates a new ContactList.
     *
     * @param $contacts array  an array of contacts, each being an array with keys "name" and "emailAddress"
     * @return W_Content  A new, saved "ContactList" object
     */
    public static function create($contacts) {
        $contactList = W_Content::create('ContactList');
        $contactList->my->mozzle = 'main';
        $contactList->my->contacts = serialize($contacts);
        $contactList->isPrivate = true;
        $contactList->save();
        return $contactList;
    }

    /**
     * Deletes old, unused ContactLists. Call this function periodically.
     */
    public static function cleanUp() {
        $query = XN_Query::create('Content');
        $query->filter('owner');
        $query->filter('type', '=', 'ContactList');
        $query->filter('createdDate', '<', gmdate('c', time() - 7200), XN_Attribute::DATE);
        $query->end(1);
        $query->order('random()');
        $contactLists = $query->execute();
        if ($contactLists && XG_Cache::lock('deleting-' . md5($contactLists[0]->id))) {
            XN_Content::delete($contactLists[0]);
        }
    }

    /**
     * Loads the ContactList with the given ID.
     *
     * @param $id string  the content ID of the ContactList to find
     * @return W_Content  the ContactList
     */
    public static function load($id) {
        $contactList = XG_Cache::content($id);
        if ($contactList->type != 'ContactList') { throw new Exception('Not a ContactList (5524337)'); }
        if ($contactList->contributorName != XN_Profile::current()->screenName) { throw new Exception('Not contact list owner (251793229)'); }
        return $contactList;
    }

/** xn-ignore-end 2365cb7691764f05894c2de6698b7da0 **/

}

XN_Event::listen('xn/content/delete/before', array('User', 'beforeDelete'));
