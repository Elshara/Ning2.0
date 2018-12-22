<?php
// TODO: Document this class, its functions, and their arguments [Jon Aquino 2008-05-24]

class XG_ShareHelper {

    const FACEBOOK_SHARE_URL = 'http://www.facebook.com/share.php';

    /** http://x.myspace.com/download/posttomyspacedeveloperdocumentation.pdf **/
    public static function postToMyspaceUrl($t='', $c='', $u='', $l=5) {
        $limit = 2000;

        // BAZ-8852 de-entityfy single quotes in the network name
        $cArr = preg_split('@<\s*/?\s*em\s*>@u', $c);
        if (count($cArr) == 3) {
            $cArr[1] = preg_replace('/&#039;/u', "'", $cArr[1]);
            $c = implode('', $cArr);
        }

        if (mb_strlen(urlencode($c)) > $limit) return '';
        if (mb_strlen(urlencode($c.$t)) > $limit) $t = '';
        if (mb_strlen(urlencode($c.$t.$u)) > $limit) $u = '';

        return 'http://www.myspace.com/index.cfm?fuseaction=postto&t='.urlencode($t).
                '&c='.urlencode($c).
                '&u='.urlencode($u).
                '&l='.$l;
    }

    /** http://www.facebook.com/share_partners.php **/
    public static function postToFacebookUrl($url) {
        XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
        return XG_HttpHelper::addParameter(self::FACEBOOK_SHARE_URL, 'u', rawurlencode(XG_HttpHelper::addParameter($url, 'from', 'fb')));
    }

}
?>
