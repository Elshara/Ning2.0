<?php
/*  $Id: $
 *
 *  Displays the calendar for the specific month
 *
 *  Parameters:
 *		$month		YYYY-MM
 *		$days		hash<day:count>
 *		$embed		bool				If TRUE, do not display some extra CSS
 *		$user		string				If not empty, user-specific calendar links are generated
 */
$wdays					= XG_DateHelper::weekdays();
$wdaysSh				= XG_DateHelper::weekdaysShort();
list($y,$m)				= explode('-',$month);
if (!$user) {
	$urlPrefix = $this->_buildUrl('event','listByDate',"?date=$month-");
} else {
	$urlPrefix = $this->_buildUrl('event','listUserEventsByDate',"?user=".urlencode($user)."&date=$month-");
}
list($thisMonth,$thisDay)= explode(' ',xg_date('Y-m j'));
?>
<?php if (!$embed) {?><div class="xg_module nopad"> <div class="xg_module_body"><?php }?>
		<div class="calendar">
			<div>
			  <span class="calendar_head xg_module_head">
			    <span class="month"><?php echo XG_DateHelper::months($m)?></span>&nbsp;<span class="year"><?php echo $y?></span>
			  </span>
				<table>
					<thead>
						<tr><?php foreach ($wdays as $k=>$v) echo "<th title=\"$v\">$wdaysSh[$k]</th>"?></tr>
					</thead>
					<tbody>
<?php
				$i = 0; foreach (XG_DateHelper::calendar($y,$m) as $week) {
					echo '<tr>';
					foreach ($wdays as $k=>$tmp) {
						if (!isset($week[$k])) {
							echo '<td></td>';
						} else {
							$d 		= $week[$k];
							$res 	= strcmp($month,$thisMonth);
							if ($res < 0 || ($res == 0 && $d < $thisDay)) { $css = 'past xg_lightfont'; }
							elseif ($res == 0 && $d==$thisDay) { $css = 'present'; }
							else { $css = 'future'; }
							echo '<td class="'.$css.'">'.($days[$d] ? "<a href=\"$urlPrefix$d\">$d</a>" : $d).'</td>';
						}
					}
					echo '</tr>';
					$i++;
				}
				for (;$i<6;$i++) {
					echo '<tr>'.str_repeat('<td>&nbsp;</td>',7).'</tr>';
				}
?>
					</tbody>
				</table>
			</div>
		</div>
<?php if (!$embed) {?></div></div><?php }?>
