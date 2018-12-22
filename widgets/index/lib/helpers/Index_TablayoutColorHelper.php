<?php

class Index_TablayoutColorHelper {

    /**
     * Generate default colors for the tab manager sub tab menu
     *
     * @return          array       array of default colors for XG_TabLayout @see XG_TabLayout()
     */
    public static function getDefaultColors() {
        W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');
        $defaults = array();
        $imagePaths = array();
        Index_AppearanceHelper::getAppearanceSettings(NULL, $defaults, $imagePaths);
        $textColor = self::rgbToHsl(self::hexToDec($defaults['moduleBodyTextColor']));
        $backgroundColor = self::rgbToHsl(self::hexToDec($defaults['pageBgColor']));
        while (abs($textColor[2] - $backgroundColor[2]) < .35) {
            if($textColor[2] > $bgColor[2]){
                $textColor[2] += min(1 - $textColor[2], 0.02);
                $backgroundColor[2] -= min($backgroundColor[2], 0.02);
            } else {
                $backgroundColor[2] += min(1 - $backgroundColor[2], 0.02);
                $textColor[2] -= min($textColor[2], 0.02);
            }
        }

        $textColorHover = array($textColor[0], $textColor[1], $textColor[2]);
        $backgroundColorHover = array($backgroundColor[0], $backgroundColor[1], $backgroundColor[2]);
        if ($textColorHover[2] > $backgroundColorHover[2]) {
            // whiter text on darker background
            $textColorHover[2] += .2;
            $textColorHover[2] = min($textColorHover[2], 1);
            $backgroundColorHover[2] += .075;
            $backgroundColorHover[2] = min($backgroundColorHover[2], 1);
            $backgroundColor[2] -= .075;
            $backgroundColor[2] = max($backgroundColor[2], 0);
        } else {
            // darker text on lighter background
            $textColor[2] += .2;
            $textColor[2] = min($textColor[2], 1);
            $backgroundColor[2] -= .05;
            $backgroundColor[2] = max($backgroundColor[2], 0);
            if($backgroundColorHover[1] < .1 && $backgroundColorHover[2] > .9) {
                // if color is almost white, take siteBgColor
                $backgroundColorHover = self::rgbToHsl(self::hexToDec($defaults['siteBgColor']));
                if($backgroundColorHover[1] > .25) {
                    $backgroundColorHover[1] = .25;
                }
                $backgroundColorHover[2] = .85;
            }
        }
        return array(   'textColor'             => self::decToHex(self::hslToRgb($textColor)),
                        'textColorHover'        => self::decToHex(self::hslToRgb($textColorHover)),
                        'backgroundColor'       => self::decToHex(self::hslToRgb($backgroundColor)),
                        'backgroundColorHover'  => self::decToHex(self::hslToRgb($backgroundColorHover)));
    }



    /**
     * Convert an RGB hex color string to an array of three values in decimal
     *
     * @param   $hex    string      hex color string, like "#aabbcc"
     * @return          array       array of 3 decimal values 0-255, like array(170, 187, 204)
     */
    public static function hexToDec($hex){
        $hex = preg_replace('/[^0-9a-fA-F]/u', '', $hex);

        // color strings like f2c map to ff22cc
        if (mb_strlen($hex) < 6) {
            $first = mb_substr($hex,0,1);
            $second = mb_substr($hex,1,1);
            $third = mb_substr($hex,2,1);
            $hex = $first . $first . $second . $second . $third . $third;
        }
        return array(hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));    /** @non-mb */
    }

    /**
     * Convert an array of three decimal color values to an RGB hex color string
     *
     * @param   $dec        array       array of 3 decimal values 0-255, like array(170, 187, 204)
     * @return              string      hex color string, like '#aabbcc'
     */
    public static function decToHex($dec){
        return '#' . sprintf("%02X",round($dec[0])) . sprintf("%02X",round($dec[1])) . sprintf("%02X",round($dec[2]));
    }

    /**
     * Convert an array of three decimal color values in RGB to an array of three values in HSL
     *
     * @param   $rgb        array       array of 3 decimal values 0-255, like array(170, 187, 204)
     * @return              array       array of 3 values representing an HSL color, like array(0.583, 0.25, 0.733)
     */
    public static function rgbToHsl($rgb){

        $clrR = ($rgb[0] / 255);
        $clrG = ($rgb[1] / 255);
        $clrB = ($rgb[2] / 255);

        $clrMin = min($clrR, $clrG, $clrB);
        $clrMax = max($clrR, $clrG, $clrB);
        $deltaMax = $clrMax - $clrMin;
        $L = ($clrMax + $clrMin) / 2;

        if (0 == $deltaMax) {
            $H = 0;
            $S = 0;
        } else {
            if (0.5 > $L) {
                $S = $deltaMax / ($clrMax + $clrMin);
            } else {
                $S = $deltaMax / (2 - $clrMax - $clrMin);
            }
            $deltaR = ((($clrMax - $clrR) / 6) + ($deltaMax / 2)) / $deltaMax;
            $deltaG = ((($clrMax - $clrG) / 6) + ($deltaMax / 2)) / $deltaMax;
            $deltaB = ((($clrMax - $clrB) / 6) + ($deltaMax / 2)) / $deltaMax;
            if ($clrR == $clrMax) {
                $H = $deltaB - $deltaG;
            } else if ($clrG == $clrMax) {
                $H = (1 / 3) + $deltaR - $deltaB;
            } else if ($clrB == $clrMax) {
                $H = (2 / 3) + $deltaG - $deltaR;
            }
            if (0 > $H) {
                $H += 1;
            }
            if (1 < $H) {
                $H -= 1;
            }
        }
        return array($H, $S, $L);
    }

    /**
     * Convert a hue to RGB color
     */
    private static function hueToRgb($v1,$v2,$vh) {
        if ($vh < 0) {
            $vh += 1;
        } elseif ($vh > 1){
            $vh -= 1;
        }
        if ((6 * $vh) < 1) {
            return ($v1 + ($v2 - $v1) * 6 * $vh);
        }
        if ((2 * $vh) < 1) {
            return ($v2);
        }
        if ((3 * $vh) < 2) {
            return ($v1 + ($v2 - $v1) * ((2 / 3 - $vh) * 6));
        }
        return $v1;
    }

    /**
     * Convert an array of three values representing an HSL color into an array of 3 values representing an RGB color
     *
     * @param       $hsl        array       array of 3 values representing an HSL color, like array(0.583, 0.25, 0.733)
     * @return                  array       array of 3 decimal values 0-255 representing an RGB color, like array(170, 187, 204)
     */
    public static function hslToRgb($hsl){
        $h = $hsl[0];
        $s = $hsl[1];
        $l = $hsl[2];
        if ($s == 0) {
            $r = $l * 255;
            $g = $r;
            $b = $r;
            return array($r, $g, $b);
        } else {
            $v2 = 0;
            $v1 = 0;
            if ($l < 0.5) {
                $v2 = $l * (1 + $s);
            } else {
                $v2 = ($l + $s) - ($s  * $l);
            }
            $v1 = 2 * $l - $v2;
            $r = 255 * self::hueToRgb($v1, $v2, $h + (1/3));
            $g = 255 * self::hueToRgb($v1, $v2, $h);
            $b = 255 * self::hueToRgb($v1, $v2, $h - (1/3));
            return array($r, $g, $b);
        }
    }
}
