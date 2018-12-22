<?php

class Index_ClearspringHelper {

    /**
     *  Returns the Custom CSS URL to be used with clearspring post content buttons.
     */
    public static function getClearspringCssUrl() {
        return xg_cdn('/xn_resources/instances/main/css/clearspring.css?'.W_Cache::getWidget('main')->config['userCssVersion']);
    }

    /**
     *  Returns the custom clearspring CSS filename.
     */
    public static function getClearspringCssFilename() {
        return $_SERVER['DOCUMENT_ROOT'] .'/xn_resources/instances/main/css/clearspring.css';
    }

    public function setClearspringCss($css) {
        //  store on filesystem
        $filename = self::getClearspringCssFilename();
        @mkdir(dirname($filename));
        file_put_contents($filename, $css);
    }

    public static function queryStringToJson($query) {
        $result = '{';
        parse_str(html_entity_decode($query), $contents);
        $pairs = array();
        foreach ($contents as $key=>$value){
            if (($key == 'video_smoothing') || ($key == 'layout')) continue;
            if (($key == 'networkName') || ($key == 'href')) {
                $pairs[] = $key . ':"' . $value . '"';
            } else if ($key == 'playlist_url'){
                $pairs[] = $key . ':"' . rawurlencode(rawurlencode($value)) . '"';
            }else {
                $pairs[] = $key . ':"' . rawurlencode($value) . '"';
            }
        }
        $result .= implode(', ', $pairs) . '}';
        return $result;
    }
    
    /**
    * Return the config string containing the flashvars parapmters of a widget given it's embed HTML
    **/
    public function extractConfigJson($embedCode, $extra = '') {
        preg_match('@FlashVars="([^"]*)@ui', $embedCode, $matches);
        $flashVars = $matches[1];
        return Index_ClearspringHelper::queryStringToJson($flashVars . $extra);
    }
    
    public function getClearspringCssTemplate() {
        return '
            /* BACKGROUND AND BORDER */ 
            #launchpad { 
                background: #FFFFFF; 
                border:1px solid #bbbbbb; 
            } 

            /* HIDE TABS */ 
            #tabs { 
                position: absolute; 
                left: -1000px; 
            } 

            /* BUTTONS */ 
            #launchpad .buttons a { 
                color: #0066CC; 
            } 

            /* TEXT COLOR */ 
            #launchpad .lightbox div, 
            #launchpad .lightbox h2 { 
                color:#666666; 
            }
';
    }

}