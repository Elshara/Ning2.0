<?php
/**
 * The pagination links display the first 3 pages, then links to the
 * 7 around the current page , then links to the last 3.
 *
 * In practice this means:
 *         If there are <= 13 pages, links to all pages are shown.
 *         For > 13 pages, link to first 3, (current/2 - 3) -> (current/2 + 3), last 3.
 *
 * @param targetUrl The url to use when the page has been changed; may contain parameters
 * @param pageParamName The name of the url parameter to receive the target page
 * @param curPage The current page to show
 * @param numPages The total number of pages
 */
XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
XG_PaginationHelper::outputPaginationProper($targetUrl, $pageParamName, $curPage, $numPages);
