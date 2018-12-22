<?php

/**
 * Color functions from external libraries for computing color matching
 * Used for generating theme css properties for the iPhone
 * 
 * http://www.easyrgb.com website for colorspace transform formulae  
 */

class Index_ColorHelper {

	public static function _hue_rgb($v1,$v2,$vH) {
	    if ($vH<0)    $vH+=1;
	    if ($vH>1)    $vH-=1;
	    if (($vH*6)<1)
	        return ($v1+($v2-$v1)*6*$vH);
	    if (($vH*2)<1)
	        return $v2;
	    if (($vH*3)<2)
	        return ($v1+($v2-$v1)*((2/3)-$vH)*6);
	    return $v1;
	}
	
	public static function rgb_hsl($r,$g,$b) { // $r,$g,$b entre 0 et 255
	    $var_R=($r/255);
	    $var_G=($g/255);
	    $var_B=($b/255);
	    $var_Min=min($var_R,$var_G,$var_B);
	    $var_Max=max($var_R,$var_G,$var_B);
	    $del_Max=$var_Max-$var_Min;
	
	    $l=($var_Max+$var_Min)/2;
	
	    if ($del_Max==0) {
	        $h=0;
	        $s=0;
	    }
	    else {
	        if ($l<0.5)
	            $s=$del_Max/($var_Max+$var_Min);
	        else
	            $s=$del_Max/(2-$var_Max-$var_Min);
	
	        $del_R=((($var_Max-$var_R)/6)+($del_Max/2))/$del_Max;
	        $del_G=((($var_Max-$var_G)/6)+($del_Max/2))/$del_Max;
	        $del_B=((($var_Max-$var_B)/6)+($del_Max/2))/$del_Max;
	
	        if ($var_R==$var_Max)
	            $h=$del_B-$del_G;
	        elseif ($var_G==$var_Max)
	            $h=(1/3)+$del_R-$del_B;
	        elseif ($var_B==$var_Max)
	            $h=(2/3)+$del_G-$del_R;
	        if ($h<0)        $h+=1;
	        if ($h>1)        $h-=1;
	    }
	    return array($h,$s,$l); // $h,$s,$l entre 0 et 1
	}
	public static function hsl_rgb($h,$s,$l) { // $h,$s,$l entre 0 et 1
	    $h=$h-floor($h); // h est entre 0 et 1
	    $s=$s-floor($s); // s est entre 0 et 1
	    $l=$l-floor($l); // l est entre 0 et 1
	    //echo $h." ".$s." ".$l."<br>\n";
	    if ($s==0) {
	        $r=$l*255;
	        $g=$l*255;
	        $b=$l*255;
	    }
	    else {
	        if ($l<0.5)
	            $var_2=$l*($l+$s);
	        else
	            $var_2=($l+$s)-($s*$l);
	        $var_1=2*$l-$var_2;
	        $r=255*self::_hue_rgb($var_1,$var_2,$h+(1/3));
	        $g=255*self::_hue_rgb($var_1,$var_2,$h);
	        $b=255*self::_hue_rgb($var_1,$var_2,$h-(1/3));
	    }
	    return array($r,$g,$b); // $r,$g,$b entre 0 et 255
	}
	public static function rgb_xyz($r,$g,$b) { // $r,$g,$b entre 0 et 255
	
	    $r /= 255;
	    $g /= 255;
	    $b /= 255;
	
	    $r=(( $r > 0.04045 )?pow(( $r + 0.055 ) / 1.055,2.4):($r/12.92))*100;
	    $g=(( $g > 0.04045 )?pow(( $g + 0.055 ) / 1.055,2.4):($g/12.92))*100;
	    $b=(( $b > 0.04045 )?pow(( $b + 0.055 ) / 1.055,2.4):($b/12.92))*100;
	    
	    //Observer. = 2¡, Illuminant = D65
	    $x = $r * 0.4124 + $g * 0.3576 + $b * 0.1805;
	    $y = $r * 0.2126 + $g * 0.7152 + $b * 0.0722;
	    $z = $r * 0.0193 + $g * 0.1192 + $b * 0.9505;
	    
	    return array($x,$y,$z);
	}
	public static function xyz_rgb($x,$y,$z) { // x entre 0 et 95.047, y entre 0 et 100, z entre 0 en 108.883
	    
	    $x /= 100;        //X = From 0 to ref_X
	    $y /= 100;        //Y = From 0 to ref_Y
	    $z /= 100;        //Z = From 0 to ref_Y
	
	    $r = $x *  3.2406 + $y * -1.5372 + $z * -0.4986;
	    $g = $x * -0.9689 + $y *  1.8758 + $z *  0.0415;
	    $b = $x *  0.0557 + $y * -0.2040 + $z *  1.0570;
	
	    $r=(( $r > 0.0031308 )?(1.055 * pow($r,1/2.4 ) - 0.055):($r*12.92))*255;
	    $g=(( $g > 0.0031308 )?(1.055 * pow($g,1/2.4 ) - 0.055):($g*12.92))*255;
	    $b=(( $b > 0.0031308 )?(1.055 * pow($b,1/2.4 ) - 0.055):($b*12.92))*255;
	    
	    return array($r,$g,$b);
	}
	public static function xyz_luv($x,$y,$z) { // XYZ Ñ> CIE-L*uv
	    $var_U = ( 4 * $x ) / ( $x + ( 15 * $y ) + ( 3 * $z ) );
	    $var_V = ( 9 * $y ) / ( $x + ( 15 * $y ) + ( 3 * $z ) );
	
	    $var_Y = $y / 100;
	    $var_Y=( $var_Y > 0.008856 )?pow( $var_Y , 1/3 ):( 7.787*$var_Y  + 16/116 );
	        
	    $ref_X =  95.047;        //Observer= 2¡, Illuminant= D65
	    $ref_Y = 100.000;
	    $ref_Z = 108.883;
	    
	    $ref_U = ( 4 * $ref_X ) / ( $ref_X + ( 15 * $ref_Y ) + ( 3 * $ref_Z ) );
	    $ref_V = ( 9 * $ref_Y ) / ( $ref_X + ( 15 * $ref_Y ) + ( 3 * $ref_Z ) );
	    
	    $l = ( 116 * $var_Y ) - 16;
	    $u = 13 * $l * ( $var_U - $ref_U );
	    $v = 13 * $l * ( $var_V - $ref_V );
	    return array($l,$u,$v);
	}
	public static function luv_xyz($l,$u,$v) { // CIE-L*uv Ñ> XYZ
	
	    $var_Y = ( $l + 16 ) / 116;
	    $var_Y= ( pow($var_Y,3) > 0.008856 )?pow($var_Y,3):(( $var_Y - 16/116 ) / 7.787);
	    
	    $ref_X =  95.047;      //Observer= 2¡, Illuminant= D65
	    $ref_Y = 100.000;
	    $ref_Z = 108.883;
	    
	    $ref_U = ( 4 * $ref_X ) / ( $ref_X + ( 15 * $ref_Y ) + ( 3 * $ref_Z ) );
	    $ref_V = ( 9 * $ref_Y ) / ( $ref_X + ( 15 * $ref_Y ) + ( 3 * $ref_Z ) );
	    
	    $var_U = $u / ( 13 * $l ) + $ref_U;
	    $var_V = $v / ( 13 * $l ) + $ref_V;
	    
	    $y = $var_Y * 100;
	    $x =  - ( 9 * $y * $var_U ) / ( ( $var_U - 4 ) * $var_V  - $var_U * $var_V );
	    $z = ( 9 * $y - ( 15 * $var_V * $y ) - ( $var_V * $x ) ) / ( 3 * $var_V );
	    return array($x,$y,$z);
	}
	public static function rgb_cmy($r,$g,$b) { // r,g,b entre 0 et 255
	    return array(1 - ( $r / 255 ),1 - ( $g / 255 ),1 - ( $b / 255 )); // c,m,y entre 0 et 1
	}
	public static function cmy_rgb($c,$m,$y) { // c,m,y entre 0 et 1
	    return array(( 1 - $c ) * 255,( 1 - $m ) * 255,( 1 - $y ) * 255); // r,g,b entre 0 et 255
	}
	public static function cmy_cmyk($c,$m,$y) {
	    $var_K = min($c,$m,$y);
	    $c = ( $c - $var_K ) / ( 1 - $var_K );
	    $m = ( $m - $var_K ) / ( 1 - $var_K );
	    $y = ( $y - $var_K ) / ( 1 - $var_K );
	    $k = $var_K;
	    return array($c,$m,$y,$k);
	}
	public static function cmyk_cmy($c,$m,$y,$k) {
	    $c = ( $c * ( 1 - $k ) + $k );
	    $m = ( $m * ( 1 - $k ) + $k );
	    $y = ( $y * ( 1 - $k ) + $k );
	    return array($c,$m,$y);
	}
	
	public static function bound_value($v,$min,$max) {
	    if ($v<$min)    return $min;
	    if ($v>$max)    return $max;
	    return $v;
	}
	
	
	/**
	 * Color functions from http://juicystudio.com/services/colourcontrast.php
	 * 
	 */
	
	  public static function contrast($color) {
	    // detect contrast of $color for color brightness formula
	    $c = new color();
	    $c -> set_from_rgbhex($color);
	    $c2=$c->get_rgb();
	    // echo '/*r:' . $c2[0] . ', g:' . $c2[1] . ', b:' . $c2[2] . "*/";
	    $c2[0] = $c2[0] * 299;
	    $c2[1] = $c2[1] * 587;
	    $c2[2] = $c2[2] * 114;
	    $colorBrightness = ($c2[0] + $c2[1] + $c2[2]) / 1000;
	    // echo $colorBrightness;
	    //echo "\n  /* color brightness score: " . $colorBrightness . "*/"; 
	    return $colorBrightness;
	  }
	  public static function doesItContrast($color1, $color2) {
	    //color brightness formula is (Red value X 299) + (Green value X 587) + (Blue value X 114)) / 1000
	    //The difference between the background brightness, and the foreground brightness should be greater than 125.
	    $brightnessContrast = abs(self::contrast($color1) - self::contrast($color2));
	    //echo "\n  /* compare contrast score with #FFF: " . $brightnessContrast . "*/\n"; 
	    if($brightnessContrast > 125) {
	      // echo '  /* color contrasts with #FFF */' . "\n";
	      return true;
	    } else {
	      $brightnessContrast = abs(self::contrast($color1) - self::contrast('#222222'));
	      //echo "\n  /* compare contrast score with #222: " . $brightnessContrast . "*/\n"; 
	      // echo '  /* color contrasts with #222 */' . "\n";
	      return false;
	    }
	  }
	  public static function returnContrastColor($color1, $color2) {
	    if(self::doesItContrast($color1, $color2) == true) {
	      return $color2;
	    } else {
	      return '#222222';
	    }
	  }
	  public static function colorDifference($color1, $color2) {
	    //color difference formula is (maximum (Red value 1, Red value 2) - minimum (Red value 1, Red value 2)) + (maximum (Green value 1, Green value 2) - minimum (Green value 1, Green value 2)) + (maximum (Blue value 1, Blue value 2) - minimum (Blue value 1
	 	//Blue value 2))
	    //The difference between the background colour and the foreground colour should be greater than 500.
	    $c1 = new color();
	    $c2 = new color();
	    $c1 -> set_from_rgbhex($color1);
	    $c2 -> set_from_rgbhex($color2);
	    $c11=$c1->get_rgb();
	    $c22=$c2->get_rgb();
	    $colorDifference = max($c11[0], $c22[0]) - min($c11[0], $c22[0]) + max($c11[1], $c22[1]) - min($c11[1], $c22[1]) + max($c11[2], $c22[2]) - min($c11[2], $c22[2]);
	    //echo "\n/*                 Color Difference is: " . $colorDifference . "*/ \n";
	    return $colorDifference;
	  }
	  public static function compareLuminosity($color1, $color2) {
	    // from http://juicystudio.com/article/luminositycontrastratioalgorithm.php
	    //(L1+.05) / (L2+.05) where L is luminosity and is defined as .2126*R + .7152*G + .0722B using linearised R, G, and B values. Linearised R (for example) = (R/FS)^2.2 where FS is full scale value (255 for 8 bit color channels). L1 is the higher value (
		//f text or background) and L2 is the lower value.
	    //Text or diagrams and their background must have a luminosity contrast ratio of at least 5:1 for level 2 conformance to guideline 1.4, and text or diagrams and their background must have a luminosity contrast ratio of at least 10:1 for level 3 confor
		//ance to guideline 1.4.
	    $c1 = new color();
	    $c2 = new color();
	    $c1 -> set_from_rgbhex($color1);
	    $c2 -> set_from_rgbhex($color2);
	    $c11=$c1->get_rgb();
	    $c22=$c2->get_rgb();
	    $c11[0] = pow(($c11[0]/255),(2.2)) * .2126;
	    $c11[1] = pow(($c11[1]/255),(2.2)) * .7152;
	    $c11[2] = pow(($c11[2]/255),(2.2)) * .0722;
	    $c22[0] = pow(($c22[0]/255),(2.2)) * .2126;
	    $c22[1] = pow(($c22[1]/255),(2.2)) * .7152;
	    $c22[2] = pow(($c22[2]/255),(2.2)) * .0722;
	    $l1 = $c11[0] + $c11[1] + $c11[2];
	    $l2 = $c22[0] + $c22[1] + $c22[2];
	    if ($l1 > $l2) {
	      $luminosity = ($l1 + 0.05)/($l2 + 0.05);
	    } else {
	      $luminosity = ($l2 + 0.05)/($l1 + 0.05);
	    }
	    //echo "\n/*                 Color Luminosity is " . $luminosity . "*/ \n";
	  }
	  
	  public static function rgbhex($red, $green, $blue) {
	    // force the passed value to be numeric by adding zero
	    // use max and min to limit the number to between 0 and 255
	    // shift the number to make it the correct future hex value
	    $red = 0x10000 * max(0,min(255,$red+0));
	    $green = 0x100 * max(0,min(255,$green+0));
	    $blue = max(0,min(255,$blue+0));
	    // convert the combined value to hex and zero-fill to 6 digits
	    return "#".str_pad(mb_strtoupper(dechex($red + $green + $blue)),6,"0",STR_PAD_LEFT);
	}
}
	
	class color {
	    //protected $r,$g,$b;
	    var $r,$g,$b;
	
	    function color() {
	    }
	
	    // rempli la classe
	    function set_from_rgb($r,$g,$b) {
	        $this->r=Index_ColorHelper::bound_value($r,0,255);
	        $this->g=Index_ColorHelper::bound_value($g,0,255);
	        $this->b=Index_ColorHelper::bound_value($b,0,255);
	    }
	    function set_from_rgbhex($hex) {
	        $delta=((mb_strlen($hex)==7)&&($hex{0}=="#"))?1:0;
	        $this->set_from_rgb(hexdec(mb_substr($hex,$delta,2)),
	                            hexdec(mb_substr($hex,$delta+2,2)),
	                            hexdec(mb_substr($hex,$delta+4,2)));
	    }
	    function set_from_hsl($h,$s,$l) {
	        $rgb=Index_ColorHelper::hsl_rgb($h,$s,$l);
	        $this->set_from_rgb($rgb[0],$rgb[1],$rgb[2]);
	    }
	    function set_from_xyz($x,$y,$z) {
	        $rgb=Index_ColorHelper::xyz_rgb($x,$y,$z);
	        $this->set_from_rgb($rgb[0],$rgb[1],$rgb[2]);
	    }
	    function set_from_luv($l,$u,$v) {
	        $xyz=luv_xyz($l,$u,$v); // first convert to xyz
	        $this->set_from_xyz($xyz[0],$xyz[1],$xyz[2]); // then set from xyz values
	    }
	    function set_from_cmy($c,$m,$y) {
	        $rgb=Index_ColorHelper::cmy_rgb($c,$m,$y);
	        $this->set_from_rgb($rgb[0],$rgb[1],$rgb[2]);
	    }
	    function set_from_cmyk($c,$m,$y,$k) {
	        $rgb=Index_ColorHelper::cmy_rgb($c,$m,$y,$k);
	        $this->set_from_rgb($rgb[0],$rgb[1],$rgb[2]);
	    }
	
	    // obtient la valeur
	    function get_rgb() {
	        return array($this->r,$this->g,$this->b);
	    }
	    function get_rgbhex() {
	        $rr=dechex(floor($this->r));
	        if (mb_strlen($rr)<2)    $rr="0".$rr;
	        $gg=dechex(floor($this->g));
	        if (mb_strlen($gg)<2)    $gg="0".$gg;
	        $bb=dechex(floor($this->b));
	        if (mb_strlen($bb)<2)    $bb="0".$bb;
	        return $rr.$gg.$bb;
	    }
	    function get_hsl() {
	        return Index_ColorHelper::rgb_hsl($this->r,$this->g,$this->b);
	    }
	    function get_xyz() {
	        return Index_ColorHelper::rgb_xyz($this->r,$this->g,$this->b);
	    }
	    function get_luv() {
	        $xyz=Index_ColorHelper::rgb_xyz($this->r,$this->g,$this->b);
	        return Index_ColorHelper::xyz_luv($xyz[0],$xyz[1],$xyz[2]);
	    }
	    function get_cmy() {
	        return Index_ColorHelper::rgb_cmy($this->r,$this->g,$this->b);
	    }
	    function get_cmyk() {
	        return rgb_cmyk($this->r,$this->g,$this->b);
	    }
	
	    // modifie la luminescence d'une couleur en hsl
	    function mod_l_hsl($delta_l) {
	        $hsl=$this->get_hsl();
	        $l=$hsl[2];
	        if ($delta_l>0)
	            $l=$l+(1-$l)*(($delta_l>1)?1:$delta_l);
	        if ($delta_l<0)
	            $l=$l*(($delta_l<-1)?-1:(1+$delta_l));
	        //echo $l."<br>\n";
	        $this->set_from_hsl($hsl[0],$hsl[1],$l);
	    }
	    // modifie la luminescence d'une couleur en luv
	    function mod_l_luv($delta_l) {
	        $luv=$this->get_luv();
	        $l=$luv[0];
	        if ($delta_l>0)
	            $l=$l+(1-$l)*(($delta_l>1)?1:$delta_l);
	        if ($delta_l<0)
	            $l=-$l*(($delta_l<-1)?-1:$delta_l);
	        $this->set_from_luv($l,$luv[1],$hsl[2]);
	    }
	    
	}	


?>