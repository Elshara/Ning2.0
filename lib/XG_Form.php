<?php
/**     $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *  SYNOPSIS:
 *
 *		$form = new XG_Form($defaults);
 *      echo $form->field('DESCRIPTION',array('text','name'));
 *      echo $form->field('SELECT_TITLE',array('select','name2', $values));
 *
 *  DESCRIPTION:
 *
 *      Just Another Cool Form Class.
 *
 **/
class XG_Form {
    protected $_values, $_errors;

    /**
     *  Constructor
     */
    public function  __construct(array $values = array(), array $errors = array()) {
        $this->_values	= $values;
        $this->_errors	= $errors;
    }

    //
    public function set($name,$value) { # void
        $this->_values[$name] = $value;
    }
    public function get($name) { # scalar
        return $this->_values[$name];
    }

    /**
     *  Initialize self date fields with prefix $idx to value of $date.
     *
     *  @param      $idx	string	Prefix for the fields
     *  @param		$date	string	yyyy-mm-dd
     *  @return     void
     */
    public function setDate($idx, $date) {
        list($y,$m,$d) = explode('-',$date,3);
        $this->_values[$idx."Y"] = $y;
        $this->_values[$idx."M"] = $m;
        $this->_values[$idx."D"] = $d;
    }

    /**
     *  Initialize self time fields with prefix $idx to value of $time.
     *
     *  @param      $idx	string	Prefix for the fields
     *  @param		$time	string	hh:mm h=[0,23]
     *  @return     void
     */
    public function setTime($idx, $time) {
        list($h24,$i) = explode(':',$time,2);

        if ($h24 == 12) {
            list($h12,$r) = array(12,'pm');
        } elseif ($h24 == 0) {
            list($h12,$r) = array(12,'am');
        } else {
            list($h12,$r) = array($h24%12, intval($h24/12) ? 'pm' : 'am');
        }
        // localized date
        $this->_values[$idx."H"] = $h12;
        $this->_values[$idx."I"] = $i;
        $this->_values[$idx."R"] = $r;
    }

//** Fields
    /**
     *  Returns the select control.
     *
	 *  @param		$name  	   string		Input name
     *  @param      $values    list|hash	Either hash with keys and values or just a list. It case of list the values and keys are match.
	 *  @param		$required  bool			Input is required
	 *  @param		$html      string		Extra HTML to add to the tag
     *  @return     string
     */
    public function select($name, array $values, $required = 0, $html = '') {
        $css = $required ? 'required' : '';
        $options = '';

        reset($values); $first = key($values);
        end($values); $last = key($values);

        $isList = ( $first == 0 && $last == count($values)-1 );
        $value = $this->_values[$name];
        foreach ($values as $k=>$v) {
            if ($isList) {
                $k = $v;
            }
            $options .= '<option value="'.$k.'"'.($k==$value ? ' selected="selected"':'').'>'.$v.'</option>';
        }
        return '<select id="'.$name.'" name="'.$name.'"' .
            ($css ? ' class="'.$css.'"' : '') .
            ($html ? ' ' . $html : '') .
            '>'.$options.'</select>';
    }

    /**
     *  Display the date control (language dependant)
     *
     *  @param      $name   	string	Input name
     *  @param		$fields		string	Any combination of "y","m","d"
     *  								For "y" you can also specify ":MIN:MAX" for a range (inclusive):
     *  								"y:0:3" - current year to current year+3.
     *  								"y:-100:0" - current year-100 to current year
     *  								Default is "-100:0"
	 *  @param		$required  bool		Input is required
	 *  @param		$html      string	Extra HTML to add to the tag
     *  @return     string
     */
    public function date($name, $fields, $required = 0, $html = '') {
        //!!TODO language-specific format
        $res	= '';
        if (FALSE !== mb_stripos($fields,'m')) {
            $months = array();
            $res .= $this->select($name.'M', XG_DateHelper::monthsShort(), $required, $html);
        }
        if (FALSE !== mb_stripos($fields,'d')) { $res .= $this->select($name.'D', range(1,31), $required, $html); }
        if (FALSE !== mb_stripos($fields,'y')) {
            if (preg_match('/y:(-?\d+):(-?\d+)?/u', $fields, $m)) {
                $min = $m[1];
                $max = $m[2];
            } else {
                $min = -100;
                $max = 0;
            }
            $year = date('Y');
            $res .= $this->select($name.'Y', range($year+$min,$year+$max), $required, $html);
        }
        return $res;
    }

    /**
     *  Display the time control (language dependant)
     *
     *  @param      $name      string		Input name
     *  @param		$fields	   string		Any combination of "h","i"
	 *  @param		$required  bool			Input is required
	 *  @param		$html      string		Extra HTML to add to the tag
     *  @return     string
     */
	public function time($name, $fields, $required = 0, $html = '') {
        //!!TODO language-specific format
        $res	= '';
        if (FALSE !== mb_stripos($fields,'h')) {
			$res .= $this->select($name.'H', range(1,12), $required, $html);
        }
		if (FALSE !== mb_stripos($fields,'i')) {
			$minutes = array('00','15','30','45'); // for now this fine
			$res .= ' : '.$this->select($name.'I', $minutes, $required, $html); }
        if (FALSE !== mb_stripos($fields,'h')) {
			$res .= $this->select($name.'R', array('am'=>xg_html('AM'), 'pm'=>xg_html('PM')), $required, $html);
		}
        return $res;
    }

    /**
     *  Returns the simple text field
     *
	 *  @param		$name  	   string		Input name
	 *  @param		$required  bool			Input is required
	 *  @param		$html      string		Extra HTML to add to the tag
     *  @return     string
     */
    public function text($name, $required = 0, $html = '') {
        $css = 'textfield' . ($required ? ' required' : '');
        return '<input type="text" id="'.$name.'" name="'.$name.'" class="'.$css.'" value="'.xg_xmlentities($this->_values[$name]).'"'.($html?' '.$html:'').' />';
    }

    /**
     *  Returns the hidden field
     *
	 *  @param		$name  	   string		Input name
     *  @return     string
     */
    public function hidden($name) {
        // TODO: Use xnhtmlentities instead of xg_xmlentities, which is intended for xml contexts [Jon Aquino 2008-04-02]
        return '<input type="hidden" name="'.$name.'" value="'.xg_xmlentities($this->_values[$name]).'" />';
    }

    /**
     * 	@param		$value		string		Value for radio box
     *  @return     string
     */
    public function radio($name,$value) {
        return '<input class="radio" type="radio" name="'.$name.'" value="'.xg_xmlentities($value).'"'.($value == $this->_values[$name]?' checked="checked"':'').'>';
    }

    /**
	 *  @param		$name  	   string		Input name
	 *  @param		$html      string		Extra HTML to add to the tag
     *  @return     string
     */
    public function checkbox($name, $html = '') {
        return '<input class="checkbox" type="checkbox" name="'.$name.'" value="1"'.($this->_values[$name]?' checked="checked"':'') . ($html?' '.$html:'') . '>';
    }

    /**
     *  Returns editor code.
     *
	 *  @param		$name  	   string		Input name
	 *  @param		$required  bool			Input is required
	 *  @param		$html      string		Extra HTML to add to the tag
     *  @return     string
     */
    public function editor($name, $required = 0, $html = '') {
        $css = $required ? 'required' : '';
        XG_App::ningLoaderRequire('xg.shared.SimpleToolbar');
        return
            '<div class="texteditor">'.
                '<textarea id="'.$name.'" name="'.$name.'" dojoType="SimpleToolbar"'.($css ? ' class="'.$css.'"' : '') . ($html?' '.$html:'') . '>'.
                    xg_xmlentities($this->_values[$name]).
                '</textarea>'.
            '</div>';
    }

    /**
     *  Returns image picker code.
     *
	 *  @param		$name  	   string		Input name
	 *  @param		$required  bool			Input is required
     *  @return     string
     */
    public function image($name, $required = 0) {
        XG_App::ningLoaderRequire('xg.shared.BazelImagePicker');
        return '<div class="swatch_group nofloat'.($required?' required':'').'" dojoType="BazelImagePicker" fieldname="'.$name.'"
            showUseNoImage="0" trimUploadsOnSubmit="0" allowTile="0"
            swatchWidth="23px" swatchHeight="21px"
            cssClass="swatch_group nofloat'.($required?' required':'').'"
            currentImagePath="'.xg_xmlentities($this->_values[$name]).'"></div>'.($required?'':'<br class="clear" />');
    }

    /**
     *  Returns the default form item code.
     *
     *  @param      $description   	string	Language resource to return
     *  @param		string|hash	...				If string, just displays it.
     *  										If array, treats the first item as a name of method to call and the rest as the args
     *  @return     string
     */
    public function field($description /*..args..*/) {
        $args = func_get_args();
        $name = '';
        for($i = 1, $max = count($args); $i<$max; $i++) {
            if (is_string($args[$i])) {
                $output .= $args[$i];
            } else {
                $method = array_shift($args[$i]);
                if (!$name) {
                    $name = $args[$i][0];
                }
                $output .= call_user_func_array(array($this,$method),$args[$i]);
            }
        }
        $err = isset($this->_errors[$name]) ? ' class="error"' : '';
        return '<dt'.$err.'><label for="'.$name.'">'.xg_html($description).'</label></dt><dd'.$err.'>'.$output.'</dd>';
    }

//** Static
    /**
     *  Parses date from the request in locale-specific format
     *
     *  @param      $idx   string		Index in _REQUEST
     *  @return     string(YYYY-MM-DD)
     */
    public static function parseDate($idx) {
        if (!$y = $_REQUEST[$idx."Y"]) {
            $y = date('Y');
        }
        if (!$m = $_REQUEST[$idx."M"]) {
            $m = date('m');
        }
        if (!$d = $_REQUEST[$idx."D"]) {
            $d = 1;
        }
        return checkdate($m,$d,$y) ? sprintf('%04d-%02d-%02d',$y,$m,$d) : '';
    }

    /**
     *  Parses time from the request in locale-specifc format
     *
     *  @param      $idx   string		Index in _REQUEST
     *  @return     string(HH:MM) H=[0,23]
     */
    public static function parseTime($idx) {
        if ($r = $_REQUEST[$idx."R"]) {	// 12-hour
            $h = $_REQUEST[$idx."H"];
            $h = $h == 12 ? ($r == 'am' ? 0 : 12) : $h+($r=='am'?0:12);
        } else {							// 24-hour
            $h = $_REQUEST[$idx."H"];
        }

        if (!$m = $_REQUEST[$idx."I"]) {
            $m = 0;
        }
        return sprintf('%02d:%02d',$h,$m);
    }
}
?>
