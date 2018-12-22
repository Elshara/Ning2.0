<?php
class Video_ImportHelper {
    /**
     *  Fetches the specified URL and tries to get the information about video on this page.
     *  Returns hash :
     *  	embedCode		string
     *  	title			string		Optional title
     *  	description 	string      Optional descript
     *  	tags			list		List of tags
     *
     *  @param      $url   string    Page URL
     *  @return     hash | NULL
     */
    public function parseVideoUrl($url) {
        $contents = file_get_contents($url);
        $info = array();

        // Get the embed code
        preg_match_all('#<input((?:\'[^\']*\'|\"[^"]*\"|[^>])+)>#', $contents, $inputs);
        foreach($inputs[1] as $data) {
            if (preg_match('#value\s*=\s*(\'([^\']+)\'|"([^"]+)"|(\S+))#i', $data, $m)) {
                $value = trim(html_entity_decode($m[2] . $m[3] . $m[4], ENT_QUOTES, 'UTF-8'));
                if (preg_match('#^<(object|embed)#i', $value)) {
                    $info['embedCode'] = $value;
                    break;
                }
            }
        }
        /*if (!$info['embedCode']) {
            return NULL;
        }*/

        // Get the meta information
        preg_match_all('#<meta((?:\'[^\']*\'|\"[^"]*\"|[^>])+)>#i', $contents, $meta);
        foreach($meta[1] as $data) {
            $n = $v = '';
            if (preg_match('#name\s*=\s*(\'([^\']+)\'|"([^"]+)"|(\S+))#i', $data, $m)) {
                $n = mb_strtolower(trim(html_entity_decode($m[2] . $m[3] . $m[4], ENT_QUOTES, 'UTF-8')));
            }
            if (isset($info[$n])) {
                continue;
            }
            if (preg_match('#content\s*=\s*(\'([^\']+)\'|"([^"]+)"|(\S+))#i', $data, $m)) {
                $v = trim(html_entity_decode($m[2] . $m[3] . $m[4], ENT_QUOTES, 'UTF-8'));
            }
            switch($n) {
                case 'title':
                case 'description':
                    $info[$n] = html_entity_decode($v);
                    break;
                case 'keywords':
                    $info['tags'] = array_map('trim', explode(',', html_entity_decode($v)));
                    break;
                default:
                    break;
            }
        }
        if (!$info['title'] && preg_match('#<title>(.*?)</title>#i', $contents, $m)) {
            $info['title'] = html_entity_decode(trim($m[1]));
        }
        return $info ? $info : NULL;
    }

}
