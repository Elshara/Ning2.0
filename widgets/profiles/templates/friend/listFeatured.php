<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
<?php

echo XG_GroupHelper::groupLink();
$this->renderPartial('fragment_membersNavigation', '_shared');

XG_App::includeFileOnce('/lib/XG_ListTemplateHelper.php');
XG_ListTemplateHelper::outputListPage(array(
        'cssInfix' => 'members',
        'featuredTitleText' => null,
        'featuredObjects' => null,
        'showViewAllFeaturedUrl' => null,
        'viewAllFeaturedUrl' => null,
		'titleHtml' => xg_headline(xg_text('FEATURED_MEMBERS'), array('count' => $this->numUsers)),
        'objects' => $this->users,
        'numObjects' => $this->numUsers,
        'rowSize' => 3,
        'pageSize' => $this->pageSize,
        'sortOptions' => $this->sortOptions,
        'searchUrl' => $this->searchUrl,
        'searchButtonText' => xg_html('SEARCH_MEMBERS'),
        'extraTemplateArgs' => array(),
        'feedUrl' => null,
        'feedTitle' => null,
        'feedFormat' => null,
        'noObjectsSubtitle' => null,
        'noObjectsMessageHtml' => null,
        'noObjectsLinkUrl' => null,
        'noObjectsLinkText' => null,
));
?>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
