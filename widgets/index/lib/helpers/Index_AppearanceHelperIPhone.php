<?php
W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_AppearanceHelper.php');

/**
 * Contains functions to handle css for the iPhone.
 */
class Index_AppearanceHelperIPhone {
	
	/* list of color properties that are inherited from the network theme */
	public static $inheritedColorProperties = array('ningbarColor',
													'siteBgColor',
													'headBgColor',
													'pageBgColor',
													'pageHeaderTextColor',
													'moduleHeadBgColor',
													'moduleHeadTextColor',
													'siteLinkColor');
	
	/**
	 * Returns a url pointing to the theme css file for the iPhone.  In case the css does not exist, attempt to
	 * generate it based on the current theme of the network.  If css generation fails, fall back on a default
	 * theme for use in the iPhone version of the network.
	 *
	 * @return string url of the iPhone theme css
	 */
	public static function getThemeCSSFilename() {
		$themeCSSFilename = NF_APP_BASE . '/theme' . W_Cache::getWidget('main')->config['userCssVersion'] . '_iphone.css';
		if (!file_exists($themeCSSFilename)) {
            @mkdir(dirname($themeCSSFilename));
            if (!$generatedCSS = self::generateCSSFromTheme()) {
            	return self::getDefaultThemeCSSFilename();
            }
            if (!file_put_contents($themeCSSFilename, $generatedCSS)) {
            	return self::getDefaultThemeCSSFilename();
            }
		}		
		return '/theme' . W_Cache::getWidget('main')->config['userCssVersion'] . '_iphone.css';
	}
	
	/**
	 * Returns a link to a default css theme for the iPhone, in case generation fails
	 * 
	 * @return string url to the default theme
	 */
	public static function getDefaultThemeCSSFilename() {
		W_Cache::getWidget('main')->buildResourceUrl('css/theme-iphone.css');
	}
	
	/**
	 * Reads the network theme css file, performs color matching, and generates a theme css
	 * for use in the iPhone
	 *
	 * @return string css code if successful, false otherwise
	 */
	public static function generateCSSFromTheme() {
		if (!$colors = self::getInheritedColorsFromTheme()) {
			return false;
		} 
		W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_ColorHelper.php');
		//Note: check if inactive nav's background is the same as active nav's background
		if (($colors['headBgColor'] == $colors['moduleHeadBgColor'] || $colors['moduleHeadBgColor'] == 'transparent') && $colors['headBgColor'] != 'transparent') {
			if (Index_ColorHelper::contrast($colors['headBgColor']) > 125) {
		    	$darken = true;
			}
			$navActiveBgColorNew = new color();
		    $navActiveBgColorNewRGB = new color();
		    $navActiveBgColorNew -> set_from_rgbhex($colors['headBgColor']);
		    if ($darken) {
		        $navActiveBgColorNewRGB = $navActiveBgColorNew->get_rgb();
		        //darken background by ~10%
		        $navActiveBgColorNewRGB[0]+=25;
		        $navActiveBgColorNewRGB[1]+=25;
		        $navActiveBgColorNewRGB[2]+=25;
		    } else {
		        $navActiveBgColorNewRGB = $navActiveBgColorNew->get_rgb();
		        //lighten background by ~10%
		        $navActiveBgColorNewRGB[0]-=25;
		        $navActiveBgColorNewRGB[1]-=25;
		        $navActiveBgColorNewRGB[2]-=25;
		    }
		  
		    $colors['moduleHeadBgColor'] = Index_ColorHelper::rgbhex($navActiveBgColorNewRGB[0], $navActiveBgColorNewRGB[1], $navActiveBgColorNewRGB[2]);
		    $colors['moduleHeadTextColor'] = Index_ColorHelper::returnContrastColor($colors['moduleHeadBgColor'], $colors['textColor']);
		}
		//Note: checks if nav's inactive background is the same as nav's inactive text color
		if (Index_ColorHelper::doesItContrast($colors['pageHeaderTextColor'], $colors['pageBgColor']) != true) {
			$pageHeaderTextColor = Index_ColorHelper::returnContrastColor($colors['pageBgColor'], $colors['textColor']);
		}
		
		return self::generateCSSFromTemplate($colors);
	}
	
	/**
	 * Using an array of color values, renders css code based on a theme template
	 *
	 * @param array $colors array of color values, where each color is in $inheritedColorProperties
	 * @return string css code
	 */
	public static function generateCSSFromTemplate($colors) {
		extract($colors);
		return "
#header {
  background-color:$ningbarColor;
}
#w #header, #w #header a {
  color:". Index_ColorHelper::returnContrastColor($ningbarColor, $textColor) .";
}
body {
  background-color:$siteBgColor;
}
#content.simple, #content.simple a, #w a, #w {
  color:". Index_ColorHelper::returnContrastColor($siteBgColor, $textColor) .";
}
#navigation {
  background-color:$headBgColor;
}
#navigation a {
  color:$pageHeaderTextColor;
}
ul li.title, ul li.category, ul li.section, ul li.title a, ul li.category a, ul li.section a {
  color:$pageHeaderTextColor !important;
  background-color:$headBgColor !important;
}
#navigation .lead li.this, #navigation .lead li.this a, #navigation ul.sub, #navigation ul.sub li.this a, #navigation .sub a { 
  background-color:$moduleHeadBgColor;
  color:$moduleHeadTextColor;
}

#footer {
  background-color:$ningbarColor;
}
#footer, #footer a {
  color:". Index_ColorHelper::returnContrastColor($ningbarColor, $textColor) .";
}

.title-button, .pin, input[type='checkbox'], span.checkbox {
  background-color:$siteLinkColor;
  color:". Index_ColorHelper::returnContrastColor($siteLinkColor, $textColor) ." !important;
}

/* adjusting colors for pins and marks */". (Index_ColorHelper::contrast($siteBgColor) < 125 ? "
/* Show Light */
.pin {border-color:#fff;}
.pin.add {background-position:-2px -2px;}
.pin.forward {background-position:-2px -49px;}
.mark.check {background-position:-2px -193px;}
.mark.chevron {background-position:-2px -242px;}
ul.list li, ul.list li.more, ul.detail li.more, ul.detail li.last { background: url(xn_resources/widgets/index/gfx/iphone/bg-pin-chevronLight.png) no-repeat right center; }
ul.list li.previous, ul.detail li.previous { background: url(xn_resources/widgets/index/gfx/iphone/bg-pin-chevronLight-back.png) no-repeat left center; }" :
"/* Show Dark */
.pin {border-color:#222;}
.pin.add {background-position:-2px -94px;}
.pin.forward {background-position:-2px -141px;}
.mark.check {background-position:-2px -241px;}
.mark.chevron {background-position:-2px -290px;}
.list li, .list li.more, .detail li.more, .detail li.last { background: url(xn_resources/widgets/index/gfx/iphone/bg-pin-chevronDark.png) no-repeat right center; }
.list li.previous, .detail li.previous { background: url(xn_resources/widgets/index/gfx/iphone/bg-pin-chevronDark-back.png) no-repeat left center; }" );
	}
	
	/**
	 * Processes the current network theme css file and extracts color values. Uses $inheritedColorProperties
	 * to look for color properties needed for the iPhone theme.  Returns false in case the css file
	 * cannot be read or if any of the properties are not found.
	 *
	 * @return array of color property => color value if successful, false otherwise
	 */
	public static function getInheritedColorsFromTheme() {
		$themeCssPath = Index_AppearanceHelper::getThemeCssFilename();
		if (@$themeCss = file_get_contents($themeCssPath)) {
			list($settings, $paths) = Index_AppearanceHelper::parseCss($themeCss);
			$colors = array();
            foreach ($settings as $key => $value) {
                if (in_array($key, self::$inheritedColorProperties)) {
                    $colors[$key] = '#' . str_replace('#','', $value);
                }
            }
            $colors['textColor'] = "#FFFFFF";
            return $colors;            
		}
		return false;
	}
}
?>