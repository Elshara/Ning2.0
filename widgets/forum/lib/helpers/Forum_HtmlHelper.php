<?php

/**
 * Useful functions for HTML output.
 */
class Forum_HtmlHelper {

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
            $links[] = '<a href="' . xnhtmlentities(XG_GroupHelper::buildUrl($widget->dir, 'topic', 'listForTag', array('tag' => $tag))) . '">' . xnhtmlentities(xg_excerpt($tag, 30)) . '</a>';
        }
        return implode(', ', $links);
    }

    /**
     * Cleans up the given HTML and removes Javascript.
     *
     * @param $html string  The HTML to clean
     * @return string  Valid HTML with scripts removed
     */
    function scrub($html) {
        return xg_scrub($html);
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
        if (preg_match('/.(ai|aiff|bz2|c|chm|conf|cpp|css|csv|deb|divx|doc|file|gif|gz|hlp|htm|html|iso|jpeg|jpg|js|mov|mp3|mpg|odc|odf|odg|odi|odp|ods|odt|ogg|pdf|pgp|php|pl|png|ppt|ps|py|ram|rar|rb|rm|rpm|rtf|swf|sxc|sxd|sxi|sxw|tar|tex|tgz|txt|vcf|wav|wma|wmv|xls|xml|xpi|xvid|zip)$/ui', $filename, $matches)) { return xg_cdn($widget->buildResourceUrl('gfx/fileicons/' . mb_strtolower($matches[1]) . '.gif')); }
        return xg_cdn($widget->buildResourceUrl('gfx/fileicons/file.gif'));
    }

    /**
     * Returns HTML for the tag display for the topic-detail page.
     *
     * @param $tags array  The topic's most popular tag names
     * @return The HTML, suitable for use as innerHTML for a <p> element
     */
    public static function tagHtmlForDetailPage($tags) {
        return count($tags) ? xg_html('TAGS_X_NO_STRONG', Forum_HtmlHelper::tagLinks(array_slice($tags, 0, Topic::TOP_TAGS_COUNT))) : '';
    }

    /**
     * Returns list for the given views, for initializing the combobox.
     * there is likely a better spot for this
     *
     * @param string $controller The name of the current controller (topic or category)
     * @return list
     */
	public static function getPageViewOptions($controller) {
        $filterMetadata = array();
        $filterMetadata[] = array(
                'displayText' => xg_text('CATEGORIES'),
                'url' => W_Cache::getWidget('forum')->config['forumMainStyle'] == 'categories' ? XG_GroupHelper::buildUrl(W_Cache::current('W_Widget')->dir, 'index', 'index') : XG_GroupHelper::buildUrl(W_Cache::current('W_Widget')->dir, 'category', 'listByTitle'),
                'selected' => $controller == 'category');
        $filterMetadata[] = array(
                'displayText' => xg_text('DISCUSSIONS'),
                'url' => W_Cache::getWidget('forum')->config['forumMainStyle'] == 'latestByTime' ? XG_GroupHelper::buildUrl(W_Cache::current('W_Widget')->dir, 'index', 'index') : XG_GroupHelper::buildUrl(W_Cache::current('W_Widget')->dir, 'topic', 'list'),
                'selected' => $controller == 'topic');
        return $filterMetadata;
    }

}