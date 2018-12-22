<?php

/**
 * Useful functions for HTML output.
 */
class Page_HtmlHelper {

    /**
     * Convert the tag names to a list of anchor tags separated by commas.
     *
     * @param $tags array  The tag names
     * @return string  An HTML string
     */
    public static function tagLinks($tags) {
        $widget = W_Cache::current('W_Widget');
        $links = array();
        foreach ($tags as $tag) {
            $links[] = '<a href="' . xnhtmlentities($widget->buildUrl('page', 'listForTag', array('tag' => $tag))) . '">' . xnhtmlentities(xg_excerpt($tag, 30)) . '</a>';
        }
        return implode(', ', $links);
    }

    /**
     * Cleans up html fragments using tidy
     *
     * @param $html string  The HTML to tidy
     */
    function scrub($html) {
       $tidyhtml =  tidy_repair_string($html, array('show-body-only' => true), 'utf8');
       return trim($tidyhtml);
    }

    /**
     * Converts the given file size to a format suitable for display,
     * e.g., 55 bytes, 130 KB, 1.4 MB
     *
     * @param $sizeInBytes string  The size of the file, in bytes
     * @return string  The file size converted to friendlier display text
     */
    function fileSizeDisplayText($sizeInBytes) {
        if ($sizeInBytes < 1024) { return xg_text('N_KB', 1); }
        if ($sizeInBytes < 1024 * 1024) { return xg_text('N_KB', round($sizeInBytes/1024)); }
        return xg_text('N_MB', round($sizeInBytes/1024/1024, 1));
    }

    /**
     * Returns the URL of an appropriate icon for the given filename.
     *
     * @param $filename string  The filename (no path necessary)
     * @return string  The URL of an image to use as an icon
     */
    function attachmentIconUrl($filename) {
        $widget = W_Cache::current('W_Widget');
        if (preg_match('/.(bmp|doc|gif|jpg|m4v|mov|mp3|mp4|pdf|png|ppt|psd|txt|wav|web|wmv|xls|zip)$/ui', $filename, $matches)) { return xg_cdn($widget->buildResourceUrl('gfx/bigicons_' . mb_strtolower($matches[1]) . '.gif')); }
        return xg_cdn($widget->buildResourceUrl('gfx/bigicons_web.gif'));
    }


    public static function prettyDate($date) {
        return xg_elapsed_time($date);
    }

    /**
     * Return the HTML for the person's full name, linked to their profile page or pages page.
     *
     * @param $screenName string  The person's username.
     * @param $sayMeForCurrentUser boolean  Whether to output "Me" instead of the person's full name. Defaults to FALSE.
     * @param $linkToProfilePage boolean  Whether to link to the person's profile page or their pages page. Defaults to TRUE (profile page).
     */
    public static function linkedScreenName($screenName, $sayMeForCurrentUser = FALSE, $linkToProfilePage = TRUE) {
        if ($linkToProfilePage) {
            XG_App::includeFileOnce('/lib/XG_HttpHelper.php');
            $url = XG_HttpHelper::profileUrl($screenName);
        } else {
            $url = W_Cache::current('W_Widget')->buildUrl('page', 'listForContributor', array('screenName' => $screenName));
        }
        return '<a class="fn url" href="' . xnhtmlentities($url) . '">' . xnhtmlentities($sayMeForCurrentUser && $screenName == XN_Profile::current()->screenName ? xg_text('ME') : Page_FullNameHelper::fullName($screenName)) . '</a>';
    }



}
