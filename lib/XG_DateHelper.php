<?php
/**     $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *  SYNOPSIS:
 *
 *      _
 *
 *  DESCRIPTION:
 *
 *      _
 *
 **/

class XG_DateHelper {
    static protected $_wdays = array(1=>'MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY');

    /**
     *  Return the list of full month names or the full name for the specified month. Month number is [1-12].
     *
     *	@param		$month	int		Month [1-12]
     *  @return     string|hash<number:name>
     */
    public static function months($month = null) {
        static $months = array(1=>'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER');
        if($month) {
            return xg_text($months[(int)$month]);
        }
        $res = array();
        foreach($months as $k=>$v) {
            $res[$k] = xg_text($v);
        }
        return $res;
    }

    /**
     *  Return the list of abbreviated month names or the abbreviated name for the specified month. Month number is [1-12].
     *
     *	@param		$month	int		Month [1-12]
     *  @return     string|hash<number:name>
     */
    public static function monthsShort($month = null) {
        static $months = array(1=>'JANUARY_SHORT', 'FEBRUARY_SHORT', 'MARCH_SHORT', 'APRIL_SHORT', 'MAY_SHORT', 'JUNE_SHORT', 'JULY_SHORT', 'AUGUST_SHORT', 'SEPTEMBER_SHORT', 'OCTOBER_SHORT', 'NOVEMBER_SHORT', 'DECEMBER_SHORT');
        if($month) {
            return xg_text($months[(int)$month]);
        }
        $res = array();
        foreach($months as $k=>$v) {
            $res[$k] = xg_text($v);
        }
        return $res;
    }

    /**
     *  Return the list of full weekday names (in the localized order). Day number is [1-7]. 1 is a Monday.
     *
     *  @return     hash<number:name>
     */
    public static function weekdays() {
        $wday	= xg_text('_FIRST_WEEKDAY');
        $res	= array();
        for($i = 0; $i<7; $i++) {
            $res[$wday] = xg_text(self::$_wdays[$wday]);
            if(++$wday>7) { $wday = 1; }
        }
        return $res;
    }

    /**
     *  Return the list of abbreviated weekday names (in the localized order). Day number is [1-7]. 1 is a Monday.
     *
     *  @return     hash<number:name>
     */
    public static function weekdaysShort() {
        $wday	= xg_text('_FIRST_WEEKDAY');
        $res	= array();
        for($i = 0; $i<7; $i++) {
            $res[$wday] = xg_text(self::$_wdays[$wday].'_SHORT');
            if(++$wday>7) { $wday = 1; }
        }
        return $res;
    }

    /**
     *  Returns days list for the specified range. Days list returned in the format YYYY-MM-DD
     *
     *  @param      $start	string		YYYY-MM-DD
     *  @param      $end	string		YYYY-MM-DD
     *  @return     list<string>
     */
    public static function dayRange($start, $end) {
        if( !preg_match('/^\d{4}-\d\d-\d\d( \d\d:\d\d)?$/u',$start) || !preg_match('/^\d{4}-\d\d-\d\d( \d\d:\d\d)?$/u',$end)) {
            return array();
        }
        $start	= intval(strtotime($start) / 86400) * 86400;
        $end	= intval(strtotime($end) / 86400) * 86400;
        $list	= array();
        if( $start && $end) {
            for(;$start<=$end;$start+=86400) {
                $list[] = date('Y-m-d',$start);
            }
        }
        return $list;
    }

    /**
     *  Returns months list for the specified range. Months list returned in the format YYYY-MM
     *
     *  @param      $start	string		YYYY-MM
     *  @param      $end	string		YYYY-MM
     *  @return     list<string>
     */
    public static function monthRange($start, $end) {
        if( !preg_match('/^\d{4}-\d\d?$/u',$start) || !preg_match('/^\d{4}-\d\d?$/u',$end))
            return array();
        $start	= self::strToYm($start);
        $end	= self::strToYm($end);
        $list 	= array();
        for (; $start<=$end; $start++) {
            $list[] = self::ymToStr($start);
        }
        return $list;
    }

    /**
     *  Returns the last day number in the specified month.
     *
     *  @param      $year	int
     *  @param		$month	int		[1,12]
     *  @return     int
     */
    public static function lastDay($year,$month) {
        // The number of days in the months (for the leap year there are small workaround)
        static $maxDays = array(0,31,28,31,30,31,30,31,31,30,31,30,31);
        return (0 == ($year%4) && 2 == $month) ? 29 : $maxDays[intval($month)];
    }

    /**
     *  Returns the list of weeks in the specified month.
     *  Every week is a hash where key is a weekday and value is a month-day
     *  Weekday = [1,7]. 1 is a Monday. Use weekdays() to output the calendar properly.
     *
     *  @param      $name   type    desc
     *  @return     list<hash<weekday:month-day>>
     */
    public static function calendar($year, $month) {
        $wday 			= date('N',mktime(0,0,0,$month,1,$year));
        $week			= 0;
        $firstWeekday	= xg_text('_FIRST_WEEKDAY');
        $res			= array();
        for($mday = 1, $max	= self::lastDay($year,$month); $mday <= $max; $mday++) {
            $res[$week][$wday] = $mday;
            if( ++$wday > 7 ) {
                $wday = 1;
            }
            if( $wday == $firstWeekday ) {
                $week++;
            }
        }
        return $res;
    }

    /**
     *  Converts string to YM-value. YM-value is a sequencial number of month.
     *
     *	@param		$str	string		YYYY-MM
     *  @return     int
     */
    public static function strToYm ($str) {
        list($y,$m) = explode('-',mb_substr($str,0,7),2);
        return $y*12+$m-1;
    }

    /**
     *  Converts YM-value to string. YM-value is a sequencial number of month.
     *
     *	@param		$ym		int
     *  @return     string
     */
    public static function ymToStr ($ym) {
        return sprintf('%04d-%02d',intval($ym/12), ($ym%12) + 1);
    }

    /**
     *  Formats date.
     *
     *  @param      $fmt	string		date() format string
     *  @param		$date	string		strtotime() date with small exception - if date has "Y-M-D H:M:S" format, it's parsed with mktime()
     *  								in order to validate wrong values. Like '2007-01-32' becomes '2007-02-01'
     *  @param		$offset string		strtotime() offset
     *  @return     string
     */
    public function format($fmt, $date, $offset = '') {
        if (preg_match('/^(\d{2,4}) - (\d\d?) - (\d\d?) (?: \s (\d\d?) : (\d\d?) (?: : (\d\d?))? )?$/xu',$date,$m)) {
            $date = mktime($m[4],$m[5],$m[6],$m[2],$m[3],$m[1]);
        } else {
            $date = strtotime($date);
        }
        if ($offset) {
            $date = strtotime($offset,$date);
        }
        return date($fmt, $date);
    }
}
?>
