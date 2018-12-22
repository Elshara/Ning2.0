<?php
/**     $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *  DESCRIPTION:
 *
 *      Helper for exporting events to an ICS file.
 *
 **/
class Events_ExportHelper {

    /**
     * Adds an element.
     *
     * @param   $name   string  Name of the element, e.g., CONTACT
     * @param   $value  string  Value of the element, e.g., Joe Smith
     * @param   $attrs  hash    Additional attributes, e.g., array('FMTTYPE'=>'image/jpeg')
     */
    public static function param($name, $value, array $attrs = array()) { # void
        $line = $name;
        foreach ($attrs as $k=>$v){
            $line .= ";" . $k . "=" . self::_quote($v);
        }
        $line .=  ":" . self::_quote($value, false);
        self::_line($line);
    }

    /**
     * Adds an element for a user.
     *
     * @param   $name       string  Name of the element, e.g., ATTENDEE
     * @param   $screenName string  Username
     * @param   $attrs      hash    Additional attributes, e.g., array('RSVP'=>'TRUE')
     */
    public static function profile($name, $screenName, array $attrs = array()) { # void
        $attrs['CN'] = xg_username($screenName);
        if (!$attrs['CN']) {
            $attrs['CN'] = $screenName;
        }
        self::param($name, xg_absolute_url(User::quickProfileUrl($screenName)), $attrs);
    }

    /**
     * Adds an element for a timestamp.
     *
     * @param   $name       string  Name of the element, e.g., DTSTAMP
     * @param   $timestamp  integer Unix timestamp
     */
    public static function datetime($name, $timestamp) { # void
        self::param($name, date('Ymd\THis\Z',$timestamp));
    }

    /**
     * Adds a multi-valued element..
     *
     * @param   $name       string  Name of the element, e.g., CATEGORIES
     * @param   $values     array   Values of the element.
     */
    public static function multi($name, $values) { # void
        foreach ($values as &$v) {
            $v = self::_quote($v);
        }
        self::_line($name.':'.join(', ',$values));
    }

    /**
     * Escapes a string.
     *
     * @param   $value  string  The value to escape
     * @param   $quote  boolean Whether to add double-quotes if needed
     * @return  string          The string with quotes, newlines, and other characters escaped
     */
    protected static function _quote($value, $quote = true) { # string
        $value = addcslashes($value,"\"\r\n");
        return ($quote && preg_match('/[^\w-]/u',$value)) ? '"'.$value.'"' : $value;
    }

    /**
     * Outputs a line.
     *
     * @param   $line   string  The line to output, splitting if necessary.
     */
    protected static function _line($line) { # void
        echo join("\r\n ",str_split($line,70)),"\r\n";
    }
}
?>
