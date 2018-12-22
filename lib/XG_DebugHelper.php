<?php

class XG_DebugHelper {
    public static function printDebug() {
        $pd_start = NF::outputTime("Entering XG_DebugHelper::printDebug()");
        ob_start();
        XN_Debug::printDebug();
        $debugOutput = trim(ob_get_clean());
        preg_match_all('@<dl class="xn-debug">(.*?)</dl>@su',$debugOutput, $queries);
        $appEndpoint = 'http://' . XN_REST::$LOCAL_API_HOST_PORT . XN_AtomHelper::APP_ATOM_PREFIX;
        print<<<_HTML_
<table class="clear">
<caption>Query Debugging Information</caption>
<tr><th>Action</th><th>Endpoint</th><th>Secs</th><th>Size</th></tr>
_HTML_;
        $totals = array('count' => 0, 'time' => 0, 'size' => 0);
        foreach ($queries[1] as $query) {
            preg_match_all('@<dt>(.*?)</dt>\s*<dd>(.*?)</dd>@su',$query, $fields, PREG_SET_ORDER);
            foreach ($fields as $match) {
                $field = $match[1];
                $value = html_entity_decode($match[2], ENT_QUOTES, 'UTF-8');
                $data[$field] = $value;
            }

            if (mb_strpos($data['Endpoint'],$appEndpoint) === 0) {
                $data['Endpoint'] = mb_substr($data['Endpoint'],mb_strlen($appEndpoint));
            }

            $stack = explode('<br/>', $data['Stack']);
            $actionStack = array();
            foreach ($stack as $i => $frame) {
                $stack[$i] = preg_replace('@^#\d+ ?@u','',trim($frame));
                if (preg_match('@([^_]+)_(.+Controller)->action\(\'([^\']+)\'.*\)@u',$stack[$i],$matches)) {

                    $actionStack[] = $matches[1].'->'.$matches[2].'::action_'.$matches[3].'()';
                }
            }
            $data['actionStack'] = array_reverse($actionStack);
            $actionString = '';
            foreach ($data['actionStack'] as $i => $action) {
                $actionString .= str_repeat('&nbsp;', 1+$i) . xnhtmlentities($action) . "\n";
            }
            $responseSize = mb_strlen($data['Response Body']);
            printf('
  <tr>
    <td>%s</td>
    <td>%s</td>
    <td>%.06f</td>
    <td>%d</td>
   </tr>',
            $actionString, xnhtmlentities($data['Endpoint']), $data['Elapsed Seconds'], $responseSize);
            $totals['count']++;
            $totals['time'] += $data['Elapsed Seconds'];
            $totals['size'] += $responseSize;

        }
        printf('<tr><td></td><td>%d Queries</td><td>%.06f Secs</td><td>%d Bytes</td></tr></table>',
               $totals['count'], sprintf('%.06f', $totals['time']), $totals['size']);
        NF::outputTime("Leaving XG_DebugHelper::printDebug()", $pd_start);
   }

   const TIMING_PLACEHOLDER = '<!-- XG_DebugHelper Timing Info Goes Here -->';
   public static function printTimingPlaceholder() { print self::TIMING_PLACEHOLDER; }

   public static function insertTimingInformation($buffer) {
       $insertTimingStart = microtime(true);
       $elapsed = defined('XG_START_TIME') ? (microtime(true) - XG_START_TIME) : null;
       $html =  "<table><caption>Timing Information</caption><tr><th>Activity</th><th>Elapsed From Section</th><th>Elapsed From Start</th></tr>";
       if (preg_match_all("@<!-- TIMING: (.+?) -->\n@u", $buffer, $matches, PREG_SET_ORDER)) {
           foreach ($matches as $match) {
               if (preg_match('@ at ([\d.]+) seconds@u', $match[1], $submatch)) {
                   $elapsedFromStart = $submatch[1];
                   $match[1] = str_replace($submatch[0], '', $match[1]);
               } else {
                   $elapsedFromStart = null;
               }
               if (preg_match('@ \(([\d.]+) seconds\)@u', $match[1], $submatch)) {
                   $elapsedFromSection = $submatch[1];
                   $match[1] = str_replace($submatch[0], '', $match[1]);
               } else {
                   $elapsedFromSection = null;
               }
               $html .= "<tr><td>$match[1]</td><td>$elapsedFromSection</td><td>$elapsedFromStart</td></tr>";
               $buffer = str_replace($match[0], '', $buffer);
           }
       } else {
           $html .= '<tr><td colspan="3">No timing info</td></tr>';
       }
       if (defined('XG_START_TIME')) {
           $html .= sprintf("<tr><td>Total PHP Execution Time</td><td></td><td>%.4f</td></tr>", $elapsed);
       }
       $html .= sprintf("<tr><td>Timing Info Insertion Time</td><td></td><td>%.4f</td></tr>", microtime(true) - $insertTimingStart);
       $html .= '</table>';
       return str_replace(self::TIMING_PLACEHOLDER, $html, $buffer);
   }


   /* An array to keep track of query log start times so we can calculate elapsed times in the queryLogAfter method */
   protected static $queryLogTiming = array();

   /**
    * Listener that takes what XN_REST fires before a request and logs it to the error log (BAZ-2074)
    *
    * @param $eventRequestId string
    * @param $curl resource
    * @param $requestHeaders array
    * @param $requestBody string
    */
   public static function queryLogBefore($eventRequestId, $curl, $requestHeaders, $requestBody) {
       $info = curl_getinfo($curl);
       if (mb_strlen($requestBody)) {
           $body = "Request Body:\n" . $requestBody . "\n";
       } else {
           $body = '';
       }
       $msg = "query-log-before (%s) @ %.04f: %s\nHeaders:\n%s\n%s";
       error_log(sprintf($msg, $eventRequestId, self::$queryLogTiming[$eventRequestId] = microtime(true), $info['url'], self::headersToString($requestHeaders), $body));
   }

   /**
    * Listener that takes what XN_REST fires after a request and logs it to the error log (BAZ-2074)
    *
    * @param $eventRequestId string
    * @param $curl resource
    * @param $responseCode integer
    * @param $responseHeaders array
    * @param $responseBody string
    */
   public static function queryLogAfter($eventRequestId, $curl, $responseCode, $responseHeaders, $responseBody) {
       $now = microtime(true);
       if (isset(self::$queryLogTiming[$eventRequestId])) {
           $elapsed = sprintf('%.04f elapsed',$now - self::$queryLogTiming[$eventRequestId]);
       } else {
           $elapsed = sprintf('@ %.04f', $now);
       }
       $info = curl_getinfo($curl);
       if (mb_strlen($responseBody)) {
           $body = "Request Body:\n" . $responseBody . "\n";
       } else {
           $body = '';
       }
       $msg = "query-log-after (%s) %s: %d %s\nHeaders:\n%s\n%s";
       error_log(sprintf($msg, $eventRequestId, $elapsed, $responseCode, $info['url'], self::headersToString($responseHeaders), $body));
   }

   protected static function headersToString($headers) {
       $s = '';
       foreach ($headers as $k => $values) {
            foreach((array)$values as $v) {
                $s .= '  ' . (mb_strlen($v) ? "$k: $v" : $k) . "\n";
            }
       }
       return $s;
   }

}
