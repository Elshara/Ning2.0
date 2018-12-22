<?php
// Renders the pagination.
//
// @param targetUrl     The url to use when the page has been changed; may contain parameters
// @param pageParamName The name of the url parameter to receive the target page
// @param curPage       The current page to show
// @param numPages      The total number of pages
// @param classes       (optional) Additional classes
//
// Use like this in a template:
//
// $this->renderPartial('fragment_pagination',
//                      array('targetUrl'       => $this->_buildUrl('index', 'index'),
//                            'pageParamName'   => 'page',
//                            'curPage'         => $this->page,
//                            'numPages'        => $this->numPages));
//
// This is the same sorting algorithm as used on the Popular Apps system page
// (http://browse.ning.com/application/any/?order=popular)
//
// If less than 12 pages, display all pages
// If more than 11 pages, then
//    If current page is less than 7 then display pages 1-9 and the last one
//    If current page is more than (last - 7) then display page 1 and pages (last - 6) - last
//    Else display page 1, pages (current - 3) - (current + 3), last
if ($numPages > 1) {
    if ($numPages <= 12) {
        $pagesToShow = range(1, $numPages);
    } else {
        $pagesToShow = array();
        $last        = $numPages - 1;

        if ($curPage < 7) {
            for ($idx = 1; $idx < 10; $idx++) {
                $pagesToShow[] = $idx;
            }
            $pagesToShow[] = '...';
            $pagesToShow[] = $last;
        } else if ($curPage > ($last - 7)) {
            $pagesToShow[] = 1;
            $pagesToShow[] = '...';
            for ($idx = $last - 9; $idx <= $last; $idx++) {
                $pagesToShow[] = $idx;
            }
        } else {
            $pagesToShow[] = 1;
            $pagesToShow[] = '...';
            for ($idx = $curPage - 3; $idx <= $curPage + 3; $idx++) {
                $pagesToShow[] = $idx;
            }
            $pagesToShow[] = '...';
            $pagesToShow[] = $last;
        }
    }
    
    /** From Photo_HtmlHelper */
    $hasSlash   = ($lastSlashPos = @mb_strrpos($targetUrl, '/')) !== false;
    $hasParams  = ($lastParamPos = @mb_strrpos($targetUrl, '?')) !== false;
    $concatChar = '?';
    if ($hasParams && (!$hasSlash || ($lastParamPos > $lastSlashPos))) {
        $concatChar = '&';
    }
?>
<ul class="pagination clear">
<?php
foreach ($pagesToShow as $curPageToShow) {
    if ($curPageToShow == '...') { ?>
  <li><span class="break">...</span></li>
<?php } else if ($curPageToShow == $curPage) { ?>
  <li><span class="this"><%= $curPageToShow %></span></li>
<?php } else { ?>
  <li><a href="<%= $targetUrl . $concatChar . rawurlencode($pageParamName) . '=' . rawurlencode($curPageToShow) %>"><%= $curPageToShow %></a></li>
<?php } /* break/this/page */?>
<?php } /* foreach */ ?>
</ul>
<?php } /* $numPages > 1 ? */ ?>
