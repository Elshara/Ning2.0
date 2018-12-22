<?php

class Index_TimeHelper {
 
    /**
     * Return a human-readable name for a given timezone offset (minutes west
     * of GMT) and optional DST observance status
     *
     * @param $offset integer Minutes west of GMT
     * @param $useDST boolean Whether DST is observed
     * @return string
     */
    public static function offsetToTimezoneName($offset, $useDST = false) {
        // Turn $useDST into 0 or 1
        $useDST = $useDST ? 1 : 0;
        // From Perl's Time::Timezone via PHP Cookbook Recipe 3.11,
        // flipped and sign-changed and converted to minutes from seconds,
        // with some help from http://www.timeanddate.com/library/abbreviations/timezones/
        static $timezones = array(
           -720 => array('NZST','NZST/NZDT'), // New Zealand
           -660 => array('GMT+11','GMT+11'), // ??
           -600 => array('EST','EST/EDT'), // Eastern Australia
           -570 => array('CST','CST/CDT'), // Central Australia
           -540 => array('GMT+9','GMT+9'), // ??           
           -480 => array('WST','WST/WDT'), // Western Australia
           -420 => array('GMT+7','GMT+7'), // ??
           -360 => array('ZP6','ZP6'), // Russia Zone 5
           -330 => array('IST','IST'), // India
           -300 => array('ZP4','ZP4'), // Russia Zone 4
           -240 => array('ZP4','ZP4'), // Russia Zone 
           -210 => array('IT','IT'), // Iran
           -180 => array('EET', 'EET/EEDT'), // Eastern Europe
           -120 => array('MEZ', 'MEZ/MESZ'), // Middle Europe (Mitteleuropäische Zeit/Mitteleuropäische Sommerzeit)
            -60 => array('CET', 'CET/CEST'), // Central European
              0 => array('GMT', 'GMT/BST'), // GMT & British Summer
             60 => array('WAT', 'WAT'), // West Africa
            120 => array('AT', 'AT'), // Azores
            210 => array('NST','NST/NDT'), // Newfoundland 
            240 => array('AST','AST/ADT'), // Atlantic
            300 => array('EST','EST/EDT'), // Eastern
            360 => array('CST','CST/CDT'), // Central
            420 => array('MST','MST/MDT'), // Mountain
            480 => array('PST','PST/PDT'), // Pacific (go Dawson Creek!)
            540 => array('AKST','AKST/AKDT'), // Alaska
            600 => array('HAST','HAST'), // Hawaii/Aleutian (no DST)
            660 => array('NST','NST'), // Nome!
            720 => array('IDLW','IDLW') // International Date Line West -- who lives here?
       );
       
       if (isset($timezones[$offset])) {
           return $timezones[$offset][$useDST];
       } else {
           $hrs = floor(abs($offset) / 60);
           $mins = $offset - ($hrs * 60);
           // Sign weirdness is because we store tzOffset in minutes west of GMT, since that's
           // what Javascript provides, but the rest of the universe thinks west of GMT is negative
           // and east of GMT is positive.
           $str = 'GMT' . (($offset > 0) ? '-' : '+') . $hrs;
           if ($mins) {
               $str .= sprintf(':%02d', $mins);
           }
           return $str;
       }
    }
}
