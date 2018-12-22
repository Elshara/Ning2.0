<?php
/**	$Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *	Common HTML functions.
 *
 **/
class XG_PageHelper {
    /**
	 *  Displays a search bar + sort options.
     *
     *  @param  $url	string	Search URL
	 *  @param	$buttonText	string	(optional) Search button text. HTML will be escaped
	 *  @param	$sortOptions	[{url,displayText,selected}]	(optional) Sort options
	 *  @param	$viewOptions	[{url,displayText,selected}]	(optional) View options
     *  @return  void
     */
    public static function searchBar($args) {
?>
<div class="xg_module module_searchbar">
	<div class="xg_module_body">
		<form action="<%=qh($args['url'])%>">
			<p class="left">
                <?php
                	// $searchUrl's query string variables will be ignored, so add them as hidden inputs [Jon Aquino 2008-02-06]
                	$urlParts = parse_url($args['url']);
                	if (mb_strlen($urlParts['query'])) {
	                	$params = array();
                		parse_str($urlParts['query'], $params);
                		foreach ($params as $key => $value) {
							echo '<input type="hidden" name="' . qh($key) .'" value="' . qh($value) .'" />';
						}
					}
                 ?>
                <input name="q" type="text" class="textfield" value="<%= qh($_GET['q']) %>" />
                <input type="submit" class="button" value="<%= qh($args['buttonText'] ? $args['buttonText'] : xg_text('SEARCH')) %>" />
			</p>
		</form>
		<?php if (is_array($args['sortOptions']) || is_array($args['viewOptions'])) { ?>
		<p class="right">
			<?php if (count($args['sortOptions']) > 1) {?>
				<%=xg_html('SORT_BY')%>
				<select onchange="window.location = this.value"<%= count($args['viewOptions']) > 1 ? ' style="margin-right: 1em;"' : ''%>>
					<?php foreach ($args['sortOptions'] as $n => $o) {
						echo '<option value="'.qh($o['url']).'" '.(($o['selected'] || $_GET['sort'] === $n) ?' selected="selected"':'').'>'.qh($o['displayText']).'</option>';
					} ?>
				</select>
			<?php } ?>
			<?php if (count($args['viewOptions']) > 1) {?>
				<%=xg_html('VIEW')%>
				<select onchange="window.location = this.value">
					<?php foreach ($args['viewOptions'] as $n => $o) {
						echo '<option value="'.qh($o['url']).'" '.(($o['selected']) ?' selected="selected"':'').'>'.qh($o['displayText']).'</option>';
					} ?>
				</select>
			<?php }?>
		</p>
		<?php } ?>
	</div>
</div>
<?php
	}

	/**
	 *  Prints sub menu (sub navigation) html. Every menu item is a hash:
	 *  	name	string	Item text
	 *  	url		string	Item URL
	 *  	add		bool	Is it an add content link?
	 *
	 *  @param	$menu	{key:{name,url,add}}	Sub menu
	 *  @param	$current	string	Current item
	 *  @return	void
	 */
	public static function subMenu($menu/*, $current = NULL -- disabled while we don't support it everywhere */) {
		$res = '';
		foreach($menu as $k=>$v) {
			$class = $v['add'] ? 'right' : ($current !== NULL && $current == $k ? 'this' : '');
			$res .= '<li'  . ($class ? " class=\"$class\"" : '') . '><a href="'.qh($v['url']).'"'.($v['add'] ? ' class="desc add"' : '').'>'.qh($v['name']).'</a></li>';
		}
		echo $res ? '<ul class="navigation">' . $res . '</ul>' : '';
	}

	/**
	 *  Display HTML for displaying a list of objects.
	 *
	 *	@param	$objects	array	The objects to display
	 *  @param	$callback	callback	Callback that prints HTML for every object (w/o <LI>).
	 *  								Callback receives a hash with arguments as a first parameters. The hash contains all items from $args,
	 *  								"object", "i" and "column" keys.
	 *  @param	$args	hash	Extra args to pass to callback.
	 *	@param	$cssInfix	string	String to use for the middle part of the CSS classes, e.g., albums
	 *	@param 	$rowSize	integer	Number of objects per row
	 *
	 *	@param	$noPagination	bool	Disable pagination
	 *	@param	$numObjects	integer	The total number of objects
	 *	@param	$pageSize	integer	The number of objects per page
	 *	@param	$paginationUrl 	string	The pagination url to pass to XG_PaginationHelper, if any
	 *  @return	void
	 */
	public static function objectList($args) {
		echo '<div class="xg_list xg_list_'.$args['cssInfix'].' xg_list_'.$args['cssInfix'].'_main">';
		$cbArgs = (array)$args['args'];
		$cbArgs['column'] = 0;
		$cbArgs['i'] = 0;
		foreach(array_chunk($args['objects'], $args['rowSize']) as $row) {
			$cbArgs['column'] = 0;
			echo '<ul>';
			foreach ($row as $object) {
				$cbArgs['object'] = $object;
				echo '<li>';
				call_user_func($args['callback'], $cbArgs);
				echo '</li>';
				$cbArgs['i']++;
				$cbArgs['column']++;
			}
			echo '</ul>';
		}
		echo '</div>';
		if (!$args['noPagination']) {
	        XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
			XG_PaginationHelper::outputPagination($args['numObjects'], $args['pageSize'], '', $args['paginationUrl']);
		}
	}


    /**
	 *  Displays featured objects.
	 *  Callback receives array_merge($args, array('object'=>$curObject)) as a first arg.
     *
	 *  @param	$titleHtml	string	Block title
	 *  @param	$cssInfix 	string	CSS suffix
	 *  @param	$objects	list	List of objects to display
	 *  @param	$viewAllUrl	string	URL for view all featured objects (if available)
	 *  @param	$callback	callback	Callback that prints HTML for every object (w/o <LI>)
	 *  								Callback receives a hash with arguments as a first parameters. The hash contains all items from $args,
	 *  								"object" and "i" keys.
	 *  @param	$args	hash	Extra args to pass to callback.
     *  @return	void
     */
	public static function featuredObjects($args) {
		if (!$args['objects']) {
			return;
		}
		$cbArgs = (array)$args['args'];
		$cbArgs['i'] = 0;
?>
<div class="xg_headline">
	<div class="tb">
		<h1><%=$args['titleHtml']%></h1>
	</div>
</div>
<div class="xg_module">
	<div class="xg_module_body">
		<div class="xg_list xg_list_<%= $args['cssInfix'] %> xg_list_<%= $args['cssInfix'] %>_feature">
			<ul>
			<?php foreach($args['objects'] as $o) {
				$cbArgs['object'] = $o;
				echo '<li>';
				call_user_func($args['callback'], $cbArgs);
				echo '</li>';
				$cbArgs['i']++;
			} ?>
			<?php?>
			</ul>
		</div>
	</div>
	<?php if ($args['viewAllUrl']) { ?>
		<div class="xg_module_foot">
			<p class="right"><a href="<%=qh($args['viewAllUrl'])%>"><%=xg_html('VIEW_ALL')%></a></p>
		</div>
	<?php }?>
</div>
<?php
    }
}
?>
