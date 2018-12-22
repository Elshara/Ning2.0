<?php

class Photo_ColorHelper {

    public static function hexToRGB($hex) {
        if ($hex[0] === "#") { $hex = mb_substr($hex, 1); }
        // Code from cory@lavacube.com, "dechex", http://ca3.php.net/manual/en/function.dechex.php
        // [Jon Aquino 2005-11-02]
        $rgb = array();
        $rgb[0] = hexdec(mb_substr($hex, 0, 2));
        $rgb[1] = hexdec(mb_substr($hex, 2, 2));
        $rgb[2] = hexdec(mb_substr($hex, 4, 2));
        return $rgb;
    }


    public static function rgbToHex($rgb) {
        // Code from cory@lavacube.com, "dechex", http://ca3.php.net/manual/en/function.dechex.php
        // [Jon Aquino 2005-11-02]
        foreach( $rgb as $val )
        {
            $out .= str_pad(dechex($val), 2, '0', STR_PAD_LEFT);
        }
        return '#' . $out;
    }

    public static function hexToHSV($hex) {
        if ($hex[0] === "#") { $hex = mb_substr($hex, 1); }
        $rgb = self::hexToRGB($hex);
        return self::RGB_to_HSV($rgb[0], $rgb[1], $rgb[2]);
    }

    // Code from Mike Snead, "RGB color to Hue/Sat/Brightness Conversion (and back)",
    // http://www.phpfreaks.com/quickcode/RGB-color-to-HueSatBrightness-Conversion-and-back/537.php
    // [Jon Aquino 2005-11-02]
    public static function RGB_to_HSV ( $r , $g , $b )
    {
        $r = $r/255;
        $g = $g/255;
        $b = $b/255;

        $MAX = max($r,$g,$b);
        $MIN = min($r,$g,$b);

        if     ($MAX == $MIN) return array(0,0,$MAX);
        if     ($r == $MAX) $HUE = ((0 + (($g - $b)/($MAX-$MIN))) * 60);
        elseif ($g == $MAX) $HUE = ((2 + (($b - $r)/($MAX-$MIN))) * 60);
        elseif ($b == $MAX) $HUE = ((4 + (($r - $g)/($MAX-$MIN))) * 60);
        if     ( $HUE < 0 ) $HUE += 360;

        return array($HUE,(($MAX - $MIN)/$MAX),$MAX);
    }

    // Code from Mike Snead, "RGB color to Hue/Sat/Brightness Conversion (and back)",
    // http://www.phpfreaks.com/quickcode/RGB-color-to-HueSatBrightness-Conversion-and-back/537.php
    // [Jon Aquino 2005-11-02]
    public static function HSV_to_RGB ( $H , $S , $V )
    {
        if ($S == 0) return array($V * 255,$V * 255,$V * 255);

        $Hi = floor($H/60);
        $f  = (($H/60) - $Hi);
        $p  = ($V * (1 - $S));
        $q  = ($V * (1 - ($S * $f)));
        $t  = ($V * (1 - ($S * (1 - $f))));

        switch ( $Hi )
        {
            case 0  : $red = $V; $gre = $t; $blu = $p; break;
            case 1  : $red = $q; $gre = $V; $blu = $p; break;
            case 2  : $red = $p; $gre = $V; $blu = $t; break;
            case 3  : $red = $p; $gre = $q; $blu = $V; break;
            case 4  : $red = $t; $gre = $p; $blu = $V; break;
            case 5  : $red = $V; $gre = $p; $blu = $q; break;
            default : exit("error -- invalid parameters\n\n");
        }

        return array(round($red * 255),round($gre * 255),round($blu * 255));
    }

    public static function grayscaleValue($hex) {
        $rgb = self::hexToRGB(self::normalize($hex));
        return self::grayscaleValueProper($rgb[0], $rgb[1], $rgb[2]) / 255;
    }

    private static function normalize($hex) {
        if (preg_match('/^#?([A-Fa-f0-9][A-Fa-f0-9][A-Fa-f0-9][A-Fa-f0-9][A-Fa-f0-9][A-Fa-f0-9])$/u', $hex, $matches)) {
            return mb_strtoupper('#' . $matches[1]);
        }
        if (preg_match('/^#?([A-Fa-f0-9])([A-Fa-f0-9])([A-Fa-f0-9])$/u', $hex, $matches)) {
            return mb_strtoupper('#' . $matches[1] . $matches[1] . $matches[2] . $matches[2] . $matches[3] . $matches[3]);
        }
        return null;
    }

    /**
     * @see David W. Fanning, "Convert RGB Image to Grayscale", http://www.dfanning.com/ip_tips/color2gray.html
     */
    private static function grayscaleValueProper($r, $g, $b) {
        return $r*.3 + $g*.59 + $b*.11;
    }

    public static function multiply ($hex, $factor) {
        $rgb = self::hexToRGB(self::normalize($hex));
        return self::rgbToHex(array(min(255, round($rgb[0] * $factor)), min(255, round($rgb[1] * $factor)), min(255, round($rgb[2] * $factor))));
    }

}
