<?php

class Index_ActivityHelper {

    /**
     * Saves the settings specified in the supplied $form array to this network's config.
     *
     * Possible vars in the array:
     *  logNewContent       = Y (or var not present for no)
     *  logNewComments      = Y (or var not present for no)
     *  logFriendships      = Y (or var not present for no)
     *  logNewMembers       = Y (or var not present for no)
     *  logProfileUpdates   = Y (or var not present for no)
     *  logNewEvents		= Y (or var not present for no)
     *  logOpenSocial       = Y (or var not present for no)
     */
    public static function setActivitySettings($form) {
        $widget = W_Cache::getWidget('activity');
        if ($form['logNewContent'] !== 'Y' && $form['logNewContent'] !== null) { throw new Exception("logNewContent must be 'Y' or null"); }
        if ($form['logNewComments'] !== 'Y' && $form['logNewComments'] !== null) { throw new Exception("logNewComments must be 'Y' or null"); }
        if ($form['logFriendships'] !== 'Y' && $form['logFriendships'] !== null) { throw new Exception("logFriendships must be 'Y' or null"); }
        if ($form['logNewMembers'] !== 'Y' && $form['logNewMembers'] !== null) { throw new Exception("logNewMembers must be 'Y' or null"); }
        if ($form['logProfileUpdates'] !== 'Y' && $form['logProfileUpdates'] !== null) { throw new Exception("logProfileUpdates must be 'Y' or null"); }
        $widget->config['logNewContent'] = ($form['logNewContent'] === 'Y' ? 'Y' : 'N');
        $widget->config['logNewComments'] = ($form['logNewComments'] === 'Y' ? 'Y' : 'N');
        $widget->config['logFriendships'] = ($form['logFriendships'] === 'Y' ? 'Y' : 'N');
        $widget->config['logNewMembers'] = ($form['logNewMembers'] === 'Y' ? 'Y' : 'N');
        $widget->config['logNewEvents'] = ($form['logNewEvents'] === 'Y' ? 'Y' : 'N');
        $widget->config['logProfileUpdates'] = ($form['logProfileUpdates'] === 'Y' ? 'Y' : 'N');
        $widget->config['logOpenSocial'] = ($form['logOpenSocial'] === 'Y' ? 'Y' : 'N');
        $widget->saveConfig();
    }
}
