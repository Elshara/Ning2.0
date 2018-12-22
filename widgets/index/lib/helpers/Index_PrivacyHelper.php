<?php

class Index_PrivacyHelper {

    /**
     * Saves the settings specified in the supplied $form array to this network's config.
     *
     * TODO: these var details are out of date ... update or remove [Thomas David Baker 2008-05-17]
     * Expected vars in the array:
     *    'privacyLevel' => 'public' or 'private'; this will not be set but will be used to determine what to set.
     *    'approveMedia' => 'yes' or null
     *
     * Possible vars in the array:
     *    'nonRegVisibility' => 'everything' or 'message' or 'homepage'
     *    'allowInvites' => 'yes' or null
     *    'allowRequests' => 'yes' or null
     */
    public static function setPrivacySettings($form) {
        $widget = W_Cache::getWidget('main');
        $openSocialWidget = W_Cache::getWidget('opensocial');
        if ($form['privacyLevel'] === 'public') {
            $validNonregVisibility = array('everything', 'message', 'homepage');
            if (! in_array($form['nonregVisibility'], $validNonregVisibility)) {
                throw new Exception('nonregVisibility must be one of: ' . implode(', ', $validNonregVisibility) . " but was " . $form['nonregVisibility']);
            }
            $widget->config['nonregVisibility'] = $form['nonregVisibility'];
            $widget->config['allowInvites'] = 'yes';
            $widget->config['allowRequests'] = 'yes';
        } else if ($form['privacyLevel'] === 'private') {
            if ($form['allowInvites'] !== 'yes' && $form['allowInvites'] !== null) { throw new Exception("allowInvites must be 'yes' or null"); }
            if ($form['allowRequests'] !== 'yes' && $form['allowRequests'] !== null) { throw new Exception("allowRequests must be 'yes' or null"); }
            $validAllowJoin = array('all','invited');
            if (! in_array($form['allowJoin'], $validAllowJoin)) {
                throw new Exception('allowJoin must be one of: ' . implode(', ', $validAllowJoin) . ' but was ' . $form['allowJoin']);
            }
            $widget->config['allowJoin'] = $form['allowJoin'];
            $widget->config['allowInvites'] = ($form['allowInvites'] === 'yes' ? 'yes' : 'no');
            $widget->config['allowRequests'] = ($form['allowRequests'] === 'yes' ? 'yes' : 'no');
        } else {
            throw new Exception("privacyLevel must be 'public' or 'private'");
        }
        $widget->config['moderate'] = ($form['approveMedia'] === 'yes' ? 'yes' : 'no');
        $widget->config['moderateGroups'] = ($form['approveGroups'] === 'yes' ? 'yes' : 'no');
        $widget->config['moderateMembers'] = ($form['approveMembers'] === 'yes' ? 'yes' : 'no');
        $widget->config['disableMusicDownload'] = ($form['enableMusicDownload'] === 'yes' ? 'no' : 'yes');
        $widget->config['onlyAdminsCanCreateGroups'] = ($form['groupCreation'] === 'yes' ? 'no' : 'yes');
        $widget->config['onlyAdminsCanCreateEvents'] = ($form['eventCreation'] === 'yes' ? 'no' : 'yes');
        $widget->config['membersCanCustomizeTheme'] = ($form['allowCustomizeTheme'] === 'yes' ? 'yes' : 'no');
        $widget->config['membersCanCustomizeLayout'] = ($form['allowCustomizeLayout'] === 'yes' ? 'yes' : 'no');
        $widget->saveConfig();
        if ($openSocialWidget->privateConfig['isEnabled'] != ($form['allow3rdPartyApplications'] === 'yes')) {
            XG_Cache::invalidate('recent-activity-items'); // let's immediately start/stop displaying OpenSocial activity items as appropriate
            $openSocialWidget->privateConfig['isEnabled'] = ($form['allow3rdPartyApplications'] === 'yes');
            $openSocialWidget->saveConfig();
        }
    }
}
