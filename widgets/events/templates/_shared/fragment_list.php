<?php
/*  $Id: $
 *
 *  Display list of events
 *
 *  Parameters:
 *		$list			XG_PagingList<Event>
 *		$wrapDate		wraps items for the same date
 *		$stdPagination	Use the default pagination instead of prev/next
 *      $feedUrl        URL for feed of most recently created events or null to hide.
 */
?>
<div class="xg_module">
	<div class="xg_module_body body_events_main">
<?php
	$wrap	= 0;
	$today	= xg_date('Y-m-d');
	$prev	= '';
	if (!$wrapDate) {
		echo '<ul class="clist noDate">';
	}
    $eventCount = count($list);
	$lastChild = !($list->isFirstPage() || $list->isLastPage());
	foreach ($list as $event) {
		// Do wrapping
		$d = mb_substr($event->my->startDate,0,10);
		if ($wrapDate && $prev != $d) {
			if ($wrap) {
				echo '</ul></div>';
			}
			$wrap 	= 1;
			$date	= strtotime($d);
			echo '<div class="wrap xg_lightborder' . $lastChild . '"><h3 class="date">',
				$d==$today ? xg_html('TODAY') : date(xg_html('EVENT_TM_FMT2'),$date) . '<span class="">'.date('l',$date).'</span>',
				'</h3><ul class="clist">';
			$prev = $d;
		}
		// Display event
		$this->renderPartial('fragment_listItem','_shared',array('event'=>$event));
	}
	echo '</ul>';
	if ($wrap) {
		echo '</div>';
	}
	if ($stdPagination) {
		XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
		XG_PaginationHelper::outputPagination($list->totalCount, $list->pageSize);
	} else {
		$this->renderPartial('fragment_pagination','_shared', array('list' => $list));
	}
?>
	</div>
	<?php if ($feedUrl) {
        xg_autodiscovery_link($this->feedUrl, xg_text('LATEST_EVENTS'), 'rss'); ?>
        <div class="xg_module_foot">
            <p class="left">
                <a class="desc rss" href="<%= xnhtmlentities($feedUrl) %>"><%= xg_html('RSS') %></a>
            </p>
        </div>
    <?php } ?>
</div>
