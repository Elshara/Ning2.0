<?php
/**
 * Show a link to a category.
 *
 * Expected vars:
 *   category - Assoc. array containing 'total', 'categoryUrl' and 'category' (name) keys.
 */
if ($category['total']) {
    $categoryLabel = xg_html(xnhtmlentities($category['category'])) . ' (' . xnhtmlentities($category['total']) . ')';
    $current = $this->categoryKey == $category['category']; 
    /* TODO: This is a fairly horrible hack to deal with the fact that two of our categories are actually sort orders ... [Thomas David Baker 2008-10-07] */ 
    $current = $current || ($_GET['sort'] == 'popular' && $category['category'] == xg_text('MOST_POPULAR'));
    $current = $current || ($_GET['sort'] == 'rating' && $category['category'] == xg_text('HIGHEST_RATED'));
    ?>
    <%= $current ? '<li><strong>' . $categoryLabel . '</strong></li>' : '<li><a href="' . xnhtmlentities($category['categoryUrl']) . '">' . $categoryLabel . '</a></li>'; %>
<?php }
