<?php

/**
 * Useful functions for HTML output.
 */
class Groups_HtmlHelper {

    /**
	 *  Returns sub navigation menu.
     *
	 *	@param		$widget	W_Widget				Profiles widget
	 *  @param      $add   	group|none				What kind of "add content" link to show.
     *  @return     hash
     */
	public static function subMenu($widget, $add = 'group') {
		$menu = array(
			'allGroups'	=> array( 'name' => xg_html('ALL_GROUPS'), 'url' => $widget->buildUrl('group', 'list') ),
			'myGroups'	=> array( 'name' => xg_html('MY_GROUPS'),  'url' => $widget->buildUrl('group', 'listForContributor', array('user' => XN_Profile::current()->screenName)) ),
		);
		switch($add) {
			case 'none': break;
			case 'group':
			default:
				if (Groups_SecurityHelper::currentUserCanSeeCreateGroupLinks()) {
					$menu['add'] = array( 'name' => xg_html('ADD_A_GROUP'), 'url' => $widget->buildUrl('group', 'new'), 'add' => 1 );
				}
				break;
		}
		return $menu;
	}
}
?>
