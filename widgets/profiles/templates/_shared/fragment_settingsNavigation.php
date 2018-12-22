<?php
/**
 * Left-hand navigation bar for the My Settings pages.
 *
 * @param string selected  the link to highlight: profile, appearance, privacy, email
 */

// load number of profile questions for BAZ-7333
$this->_widget->includeFileOnce('/lib/helpers/Profiles_ProfileQuestionHelper.php');

$links = array(
	'profile' => array('name' => xg_text('PROFILE'), 'url' => $this->_buildUrl('settings', 'editProfileInfo')),
	'appearance' => array('name' => xg_text('APPEARANCE'), 'url' => $this->_buildUrl('profile', 'edit')),
	'privacy' => array('name' => xg_text('PRIVACY'), 'url' => $this->_buildUrl('profile', 'privacySettings')),
	'email' => array('name' => xg_text('EMAIL'), 'url' => $this->_buildUrl('profile', 'emailSettings')),
);

?>
<ul class="left page_tickers">
    <?php
    foreach ($links as $name => $link) { ?>
        <li<%= $name == $selected ? ' class="this"' : '' %>><a href="<%= xnhtmlentities($link['url']) %>"><%= xnhtmlentities($link['name']) %></a></li>
    <?php
    } ?>
</ul>
