<?php
/*  $Id: $
 *
 *  Renders calendar block
 *
 *  Parameters:
 *		$calendar		hash<yyyy-mm:<day=>count>>
 *		$showUser		string				If non-empty, specifies screenName of user instead of widget-wide (for URLs only)
 *		$embed			bool				If true, do not display some CSS
 *
 */
if (!isset($embed)) {
	$embed = 0;
}
if (!isset($showUser)) {
	$showUser = '';
}

$args 	= array();
if ($embed) {
	$args['embed'] = 1;
}

if ($showUser) {
	$args['show_user'] = $showUser;
	list($min,$max) = EventAttendee::getMinMaxEventDates($showUser);
} else {
	list($min,$max) = EventWidget::getMinMaxEventDates();
}

sort($months = array_keys($calendar));

$first	= XG_DateHelper::strToYm(reset($months));
$last	= XG_DateHelper::strToYm(end($months));
$min	= XG_DateHelper::strToYm($min);
$max	= XG_DateHelper::strToYm($max);

XG_App::ningLoaderRequire('xg.events.Scroller');
?>
<div class="calendarWrap">
  <div dojoType="Scroller"
      _buttonContainer="evt_cal_btn_container"
      _nextButton="evt_cal_next"
      _prevButton="evt_cal_last"
      _prevSeqId="<%=$min<$first ? XG_DateHelper::ymToStr($first-1) : ''%>"
      _nextSeqId="<%=$max>$last ? XG_DateHelper::ymToStr($last+1) : ''%>"
      _scrollBy="1"
      _threshold="2"
  	_url="<%=$this->_buildUrl('event','getCalendar',$args)%>">
  <?php foreach ($calendar as $month=>$days) {
      $this->renderPartial('fragment_calendarMonth', '_shared', array('month'=>$month, 'days'=>$days, 'embed'=>$embed, 'user' => $showUser));
  } ?>
  </div>
  <div id="evt_cal_btn_container"<%=$embed?' class="easyclear"':' class="xg_module_foot"'%> style="display:none">
      <p class="left"><a id="evt_cal_last" href="#" style="display:none"><%= xg_html('LAST_MONTH') %></a></p>
      <p class="right"><a id="evt_cal_next" href="#" style="display:none"><%= xg_html('NEXT_MONTH') %></a></p>
  </div>
</div>