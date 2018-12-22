<?php
/*  $Id: $
 *
 *  Renders embed module content
 *
 *  Parameters:
 *      $events         XG_PagingList<Event>
 *      $settings       hash
 *      $columns        int
 *      $calendar       hash<yyyy-mm:days>
 *      $viewAllUrl
 *      $profileName    string      screenName of the profile module owner. NULL for homepage module
 *      $embed          XG_Embed    stores the module data
 */
$opts           = array('showImage'=>0, 'showExtra'=>0, 'showWrap'=>1, 'showRsvp'=>0, 'tbWrap'=>true);
$showCalendar   = 0;
$wrapDate       = 0;
switch($settings['display']) {
    case 'detail':
        $css                = 'body_detail';
        $opts['showExtra']  = 1;
        $opts['imageSize']  = 96;
        $opts['showImage']  = $columns != 1;
        $wrapDate           = $columns != 1;
        break;

    case 'list':
        $css                = 'body_list';
        $opts['showExtra']  = 0;
        $opts['imageSize']  = 36;
        $opts['showImage']  = $columns != 1;
        $wrapDate           = $columns != 1;
        break;

    case 'calendar':
        $css                = 'body_calendar';
        $opts['showWrap']   = 0;
        $opts['tbWrap']     = false;
        $showCalendar       = 1;
        break;
}
if ($settings['count'] != 0) { ?>
    <div class="xg_module_body <%=$css%>">
        <?php if (0 == count($events)) {
            if ($settings['from'] == 'featured') {
                echo '<h3>' . xg_html('THERE_ARE_NO_FEATURED_EVENTS') . '</h3>';
                echo '<p>' . xg_html('START_FEATURING_X_CLICK_Y', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildRelativeUrl('admin','featuring')) .'"') . '</p>';
            } elseif (Events_SecurityHelper::currentUserCanCreateEvent()) {
                echo '<p><a href="',$this->_buildUrl('event','new'),'" class="desc add">',xg_html('CREATE_EVENT'),'</a></p>';
            } else {
                echo '<p>' . xg_html('NO_EVENTS_TO_DISPLAY') . '</p>';
            }
        } elseif ($showCalendar) {?>
            <?php if ($calendar) { $this->renderPartial('fragment_calendar', '_shared', array('calendar' => $calendar, 'embed' => 1, 'showUser' => $profileName)); }?>
            <div class="tb">
                <ul class="clist">
                    <?php foreach ($events as $event) {  $opts['event'] = $event; $this->renderPartial('fragment_listItem','embed',$opts); }?>
                </ul>
            </div>
        <?php } elseif ($wrapDate) {
            $wrap   = 0;
            $today  = xg_date('Y-m-d');
            $prev   = '';
            $eventCount = count($events);
            $counter = 1;
            foreach ($events as $event) {
                $d = mb_substr($event->my->startDate,0,10);
                if ($wrapDate && $prev != $d) {
                    if ($wrap) {
                        echo '</ul></div>';
                    }
                    $wrap   = 1;
                    $date   = strtotime($d);
                    $lastChild = $counter == $eventCount ? ' last-child' : '';
                    echo '<div class="wrap easyclear xg_lightborder' . $lastChild . '"><div class="dategroup"><h3><a href="' . xnhtmlentities($this->_buildUrl('event', 'listByDate', array('date' => $d))) . '">',
                        $d==$today ? xg_html('TODAY') . '</a></h3>' : date(xg_html('EVENT_TM_FMT2'),$date) . '</a></h3> <p class="small">'.date('l',$date).'</p>',
                        '</div><ul class="clist">';
                    $prev = $d;
                }
                $opts['event'] = $event; $this->renderPartial('fragment_listItem','embed',$opts);
                $counter ++;
            }
            if ($wrap) {
                echo '</ul>';
                echo '</div>';
            }
        } else {?>
            <ul class="clist">
                <?php foreach ($events as $event) {  $opts['event'] = $event; $this->renderPartial('fragment_listItem','embed',$opts); }?>
            </ul>
        <?php }?>
    </div>
<?php }
if ($settings['count'] == 0 || count($events)) { ?>
    <div class="xg_module_foot">
        <ul>
            <?php if (Events_SecurityHelper::currentUserCanCreateEvent() && XN_Profile::current()->isLoggedIn()) { /* @TODO: Define a new method to check for both.  [2008-09-29 Mohan] */ ?>
                <li class="left"><a href="<%=$this->_buildUrl('event', 'new', array('cancelTarget' => XG_HttpHelper::currentUrl()))%>" class="desc add"><%= xg_html('ADD_AN_EVENT') %></a></li>
            <?php } ?>
            <?php if ($settings['count'] != 0) { ?>
                <li class="right"><a href="<%=$viewAllUrl%>"><%=xg_html('VIEW_ALL')%></a></li>
            <?php } ?>
        </ul>
    </div>
<?php
}?>
