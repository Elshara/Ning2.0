<?php

/**
 * Useful functions for displaying pagination.
 */
class XG_PaginationHelper {

    /**
     * Displays the pagination. The current page number is determined from the URL.
     *
     * @param $totalCount integer  The total number of items.
     * @param $pageSize integer  The number of items displayed per page.
     * @param $classes string  Additional CSS classes for the ul element
     * @param $url string  The URL of the current page, or NULL to determine it automatically, or # for JavaScript implementations.
     * @param $pageParamName string The name of the url parameter for the page number; defaults to 'page'
     * @param $listOnly boolean  true to output list items only, false (default) to output ul tags as well
     */
    public static function outputPagination($totalCount, $pageSize, $classes = '', $url = NULL, $pageParamName = NULL, $listOnly = false, $extraURLData = '') {
        $pagination = self::computePagination($totalCount, $pageSize, $url, $pageParamName);
        self::outputPaginationProper($pagination['targetUrl'], $pagination['pageParamName'], $pagination['curPage'], $pagination['numPages'], $classes, $listOnly, $extraURLData);
    }

    /**
     * Computes pagination values, suitable for use with outputPaginationProper().
     * The current page number is determined from the URL.
     *
     * @param $total integer  The total number of items.
     * @param $pageSize integer  The number of items displayed per page.
     * @param $url string  The URL of the current page, or NULL to determine it automatically, or # for JavaScript implementations.
     * @param $pageParamName string The name of the url parameter for the page number; defaults to 'page'
     * @return array  targetUrl, pageParamName, curPage, and numPages - see outputPaginationProper() for details.
     */
    public static function computePagination($total, $pageSize, $url = NULL, $pageParamName = NULL) {
        if (! $pageParamName) { $pageParamName = 'page'; }
        $vars = $_GET;
        if ($url && $url != '#') {
            $parsedUrl = parse_url($url);
            parse_str($parsedUrl['query'], $vars);
        }
        $currentPage = $vars[$pageParamName] ? $vars[$pageParamName] : 1;
        unset($vars['popDownMessage']);
        unset($vars[$pageParamName]);
        $path = preg_replace('/\?.*/u', '', $url ? $url : XG_HttpHelper::currentUrl());
        $path .= '?' . http_build_query($vars);
        $path = preg_replace('/&$/u', '', $path);
        // BAZ-2587 [Jon Aquino 2007-04-18]
        $path = XG_HttpHelper::removeParameter($path, 'commentId');
        if ($url == '#') { $path = '#'; }
        $pageCount = ceil($total / $pageSize);
        return array('targetUrl' => $path, 'pageParamName' => $pageParamName, 'curPage' => $currentPage, 'numPages' => $pageCount);
    }

    /**
     * Displays the pagination.
     *
     * The pagination links display the first 3 pages, then links to the
     * 7 around the current page , then links to the last 3.
     *
     * In practice this means:
     *         If there are <= 13 pages, links to all pages are shown.
     *         For > 13 pages, link to first 3, (current/2 - 3) -> (current/2 + 3), last 3.
     *
     * @param targetUrl The url of the page, or # for JavaScript implementations
     * @param pageParamName The name of the url parameter for the page number (typically "page")
     * @param curPage The current page number
     * @param numPages The total number of pages
     * @param $classes string  Additional CSS classes for the ul element
     * @param listOnly boolean  true to output only the line items, false (default) to include ul tags
     * @param $extraURLData string Extra data to append to the pagination links (such as #comments)
     */
    public static function outputPaginationProper($targetUrl, $pageParamName, $curPage, $numPages, $classes = '', $listOnly = false, $extraURLData = '') {
        if ($_GET['test_pagination']) { $numPages = 100; }
        // From ningbar/controllers/SearchControllerClass.php  [Jon Aquino 2006-11-23]
        if ($numPages > 1) {
            if ($numPages <= 13) {
                $pagesToShow = range(1, $numPages);
            } else {
                // First three
                foreach (array(1,2,3) as $i) { $pageNumbers[$i] = true; }
                if ($curPage < 4) {
                    $pivot = 4;
                } else if ($curPage >= ($numPages - 3)) {
                    $pivot = $numPages - 4;
                } else {
                    $pivot = $curPage;
                }
                // Middle
                for ($i = $pivot - 3; $i <= $pivot + 3; $i++) {
                    $pageNumbers[$i] = true;
                }
                // Last three
                foreach (range($numPages-2,$numPages) as $i) {
                    $pageNumbers[$i] = true;
                }
                $pagesToShow = array_keys($pageNumbers);
            } ?>
            <?php if (! $listOnly) { ?><ul class="pagination easyclear <%= $classes %>"><?php } ?>
                <?php
                $previousPage = null;
                foreach ($pagesToShow as $page) {
                    if ((! is_null($previousPage)) && ($page != ($previousPage + 1))) { ?>
                        <li class="break"><span>...</span></li>
                    <?php
                    }
                    $previousPage = $page;
                    if ($page == $curPage) { ?>
                        <li class="this"><span><?php echo $page ?></span></li>
                     <?php
                    } else { ?>
                        <li><a href="<%= $targetUrl == '#' ? '#' : xnhtmlentities(XG_HttpHelper::addParameter($targetUrl, $pageParamName, $page)) . $extraURLData %>"><?php echo $page ?></a></li>
                    <?php
                    }
                } ?>
            <?php if (! $listOnly) { ?></ul><?php } ?>
        <?php
        }
    }

    /**
     * Computes the start index for a query.
     *
     * @param $page integer  The page number (NULL is treated as page 1),
     * @param $pageSize integer  The number of items per page.
     * @return integer  The zero-based start index.
     */
    public static function computeStart($page, $pageSize) {
        $page = $page ? $page : 1;
        return ($page-1) * $pageSize;
    }

}
