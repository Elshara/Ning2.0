<?php xg_header($this->highlightedTab, $this->pageTitle); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
<?php

if ($this->myFriends) {
    XG_App::ningLoaderRequire('xg.profiles.embed.unfriend');
}
XG_App::includeFileOnce('/lib/XG_ListTemplateHelper.php');
XG_ListTemplateHelper::outputListPage(array(
        'cssInfix' => 'members',
        'featuredObjects' => array(),
        'isSortRandom' => true,
        'titleHtml' => xg_headline(xnhtmlentities($this->pageTitle), array('count' => $this->numUsers)), 
        'objects' => $this->users,
        'numObjects' => $this->numUsers,
        'rowSize' => 3,
        'pageSize' => $this->pageSize,
        'sortOptions' => null,
        'searchUrl' => null,
        'searchButtonText' => null,
        'extraTemplateArgs' => array(),
        'feedUrl' => null,
        'feedTitle' => null,
        'feedFormat' => null,
        'noObjectsSubtitle' => null,
        'noObjectsMessageHtml' => null,
        'noObjectsLinkUrl' => null,
        'noObjectsLinkText' => null,
        'noObjectsLinkIcon' => null
));
?>
        </div>
        <div class="xg_1col">
            <div class="xg_1col first-child"><?php xg_sidebar($this); ?></div>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
