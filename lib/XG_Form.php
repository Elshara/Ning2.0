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
    public function get($name) { # scalar|null
        return $this->_values[$name] ?? null;
    }

    /**
     *  Initialize self date fields with prefix $idx to value of $date.
     *
     *  @param      $idx	string	Prefix for the fields
     *  @param		$date	string	yyyy-mm-dd
     *  @return     void
     */
    public function setDate($idx, $date) {
        $parts = array_pad(explode('-', trim((string) $date), 3), 3, '');

        $this->_values[$idx . 'Y'] = $parts[0];
        $this->_values[$idx . 'M'] = $parts[1];
        $this->_values[$idx . 'D'] = $parts[2];
    }

    /**
     *  Initialize self time fields with prefix $idx to value of $time.
     *
     *  @param      $idx	string	Prefix for the fields
     *  @param		$time	string	hh:mm h=[0,23]
     *  @return     void
     */
    public function setTime($idx, $time) {
        $parts = array_pad(explode(':', trim((string) $time), 2), 2, '');
        $hour24 = is_numeric($parts[0]) ? (int) $parts[0] : 0;
        $minute = is_numeric($parts[1]) ? (int) $parts[1] : 0;

        $hour24 = ($hour24 % 24 + 24) % 24;
        $minute = max(0, min(59, $minute));

        if ($hour24 === 0) {
            $hour12 = 12;
            $meridiem = 'am';
        } elseif ($hour24 === 12) {
            $hour12 = 12;
            $meridiem = 'pm';
        } else {
            $hour12 = $hour24 % 12;
            $meridiem = ($hour24 >= 12) ? 'pm' : 'am';
        }

        $this->_values[$idx . 'H'] = $hour12;
        $this->_values[$idx . 'I'] = sprintf('%02d', $minute);
        $this->_values[$idx . 'R'] = $meridiem;
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
        if (is_string($required)) {
            $html = $required;
            $required = 0;
        }

        $isRequired = (bool) $required;
        $css = $isRequired ? 'required' : '';
        $options = '';
        $selectedValue = $this->_values[$name] ?? null;
        $keys = array_keys($values);
        $isList = true;
        foreach ($keys as $index => $key) {
            if ((string) $key !== (string) $index) {
                $isList = false;
                break;
            }
        }

        foreach ($values as $key => $label) {
            $value = $isList ? $label : $key;
            $optionValue = xg_xmlentities((string) $value);
            $optionLabel = xg_xmlentities((string) $label);
            $isSelected = ((string) $value === (string) $selectedValue) ? ' selected="selected"' : '';
            $options .= '<option value="' . $optionValue . '"' . $isSelected . '>' . $optionLabel . '</option>';
        }

        $fieldName = xg_xmlentities((string) $name);
        $attributes = ' id="' . $fieldName . '" name="' . $fieldName . '"';
        if ($css !== '') {
            $attributes .= ' class="' . $css . '"';
        $isList = ( $first == 0 && $last == count($values)-1 );
        $value = $this->_values[$name] ?? null;
        foreach ($values as $k=>$v) {
            if ($isList) {
                $k = $v;
            }
            $optionValue = xg_xmlentities((string) $k);
            $optionLabel = xg_xmlentities((string) $v);
            $isSelected = ((string) $k === (string) $value) ? ' selected="selected"' : '';
            $options .= '<option value="' . $optionValue . '"' . $isSelected . '>' . $optionLabel . '</option>';
        }

        $htmlAttributes = trim((string) $html);
        if ($htmlAttributes !== '') {
            $attributes .= ' ' . $htmlAttributes;
        }

        return '<select' . $attributes . '>' . $options . '</select>';
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
        if (is_string($required)) {
            $html = $required;
            $required = 0;
        }

        //!!TODO language-specific format
        $res = '';
        if (FALSE !== mb_stripos($fields, 'm')) {
            $res .= $this->select($name . 'M', XG_DateHelper::monthsShort(), $required, $html);
        }
        if (FALSE !== mb_stripos($fields, 'd')) {
            $res .= $this->select($name . 'D', range(1, 31), $required, $html);
        }
        if (FALSE !== mb_stripos($fields, 'y')) {
            if (preg_match('/y:(-?\d+):(-?\d+)?/u', $fields, $m)) {
                $min = (int) $m[1];
                $max = isset($m[2]) ? (int) $m[2] : 0;
            } else {
                $min = -100;
                $max = 0;
            }
            if ($max < $min) {
                [$min, $max] = array($max, $min);
            }
            $year = (int) date('Y');
            $res .= $this->select($name . 'Y', range($year + $min, $year + $max), $required, $html);
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
        if (is_string($required)) {
            $html = $required;
            $required = 0;
        }

        //!!TODO language-specific format
        $res = '';
        if (FALSE !== mb_stripos($fields, 'h')) {
            $res .= $this->select($name . 'H', range(1, 12), $required, $html);
        }
        if (FALSE !== mb_stripos($fields, 'i')) {
            $minutes = array('00', '15', '30', '45'); // for now this fine
            $res .= ' : ' . $this->select($name . 'I', $minutes, $required, $html);
        }
        if (FALSE !== mb_stripos($fields, 'h')) {
            $res .= $this->select($name . 'R', array('am' => xg_html('AM'), 'pm' => xg_html('PM')), $required, $html);
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
        if (is_string($required)) {
            $html = $required;
            $required = 0;
        }

        $css = 'textfield' . ((bool) $required ? ' required' : '');
        $value = $this->_values[$name] ?? '';
        $fieldName = xg_xmlentities((string) $name);
        $valueAttribute = xg_xmlentities((string) $value);

        $htmlAttributes = trim((string) $html);
        $htmlSuffix = ($htmlAttributes !== '') ? ' ' . $htmlAttributes : '';

        return '<input type="text" id="' . $fieldName . '" name="' . $fieldName . '" class="' . $css . '" value="' . $valueAttribute . '"' . $htmlSuffix . ' />';
        $css = 'textfield' . ($required ? ' required' : '');
        $value = $this->_values[$name] ?? '';

        return '<input type="text" id="'.$name.'" name="'.$name.'" class="'.$css.'" value="'.xg_xmlentities((string) $value).'"'.($html?' '.$html:'').' />';
    }

    /**
     *  Returns the hidden field
     *
	 *  @param		$name  	   string		Input name
     *  @return     string
     */
    public function hidden($name) {
        $value = $this->_values[$name] ?? '';

        return '<input type="hidden" name="'.$name.'" value="'.xnhtmlentities((string) $value).'" />';
    }

    /**
     * 	@param		$value		string		Value for radio box
     *  @return     string
     */
    public function radio($name,$value) {
        $current = $this->_values[$name] ?? null;

        return '<input class="radio" type="radio" name="'.$name.'" value="'.xg_xmlentities((string) $value).'"'.(((string) $value === (string) $current)?' checked="checked"':'').'>';
    }

    /**
	 *  @param		$name  	   string		Input name
	 *  @param		$html      string		Extra HTML to add to the tag
     *  @return     string
     */
    public function checkbox($name, $html = '') {
        $isChecked = !empty($this->_values[$name]);

        return '<input class="checkbox" type="checkbox" name="'.$name.'" value="1"'.($isChecked?' checked="checked"':'') . ($html?' '.$html:'') . '>';
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
        $value = $this->_values[$name] ?? '';

        return
            '<div class="texteditor">'.
                '<textarea id="'.$name.'" name="'.$name.'" dojoType="SimpleToolbar"'.($css ? ' class="'.$css.'"' : '') . ($html?' '.$html:'') . '>'.
                    xg_xmlentities((string) $value).
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
        $value = $this->_values[$name] ?? '';
        $classes = 'swatch_group nofloat' . ($required ? ' required' : '');
        $fieldName = xg_xmlentities((string) $name);

        return '<div class="' . $classes . '" dojoType="BazelImagePicker" fieldname="' . $fieldName . '"'
            . ' showUseNoImage="0" trimUploadsOnSubmit="0" allowTile="0"'
            . ' swatchWidth="23px" swatchHeight="21px"'
            . ' cssClass="' . $classes . '"'
            . ' currentImagePath="' . xg_xmlentities((string) $value) . '"></div>'
            . ($required ? '' : '<br class="clear" />');
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
        $output = '';
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
        $requestedYear = $_REQUEST[$idx."Y"] ?? null;
        $requestedMonth = $_REQUEST[$idx."M"] ?? null;
        $requestedDay = $_REQUEST[$idx."D"] ?? null;

        $y = (is_scalar($requestedYear) && $requestedYear !== '') ? (int) $requestedYear : (int) date('Y');
        $m = (is_scalar($requestedMonth) && $requestedMonth !== '') ? (int) $requestedMonth : (int) date('m');
        $d = (is_scalar($requestedDay) && $requestedDay !== '') ? (int) $requestedDay : 1;

        return checkdate($m,$d,$y) ? sprintf('%04d-%02d-%02d',$y,$m,$d) : '';
    }

    /**
     *  Parses time from the request in locale-specifc format
     *
     *  @param      $idx   string		Index in _REQUEST
     *  @return     string(HH:MM) H=[0,23]
     */
    public static function parseTime($idx) {
        $meridiemRaw = $_REQUEST[$idx."R"] ?? null;
        $hourRaw = $_REQUEST[$idx."H"] ?? null;
        $minuteRaw = $_REQUEST[$idx."I"] ?? null;

        $meridiem = is_scalar($meridiemRaw) ? mb_strtolower(trim((string) $meridiemRaw)) : '';
        $hourValue = (is_scalar($hourRaw) && $hourRaw !== '') ? (int) $hourRaw : 0;
        $minuteValue = (is_scalar($minuteRaw) && $minuteRaw !== '') ? (int) $minuteRaw : 0;


        $meridiem = is_scalar($meridiemRaw) ? mb_strtolower(trim((string) $meridiemRaw)) : '';
        $hourValue = (is_scalar($hourRaw) && $hourRaw !== '') ? (int) $hourRaw : 0;
        $minuteValue = (is_scalar($minuteRaw) && $minuteRaw !== '') ? (int) $minuteRaw : 0;

        if ($meridiem === 'am' || $meridiem === 'pm') {
            if ($hourValue === 12) {
                $hourValue = ($meridiem === 'am') ? 0 : 12;
            } else {
                $hourValue += ($meridiem === 'am') ? 0 : 12;
            }
        }

        return sprintf('%02d:%02d',$hourValue,$minuteValue);
    }
}
?>
