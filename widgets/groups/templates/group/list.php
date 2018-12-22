<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
		<?php XG_PageHelper::subMenu(Groups_HtmlHelper::subMenu($this->_widget)) ?>
        <?php
        XG_App::includeFileOnce('/lib/XG_ListTemplateHelper.php');
        XG_ListTemplateHelper::outputListPage(array(
                'cssInfix' => 'groups',
                'featuredTitleText' => xg_text('FEATURED_GROUPS'),
                'featuredObjects' => $this->featuredGroups['items'],
                'showViewAllFeaturedUrl' => $this->featuredGroups['totalCount'] > 5 ? true: false,
                'viewAllFeaturedUrl' => $this->_buildUrl('group', 'listFeatured'),
                'titleHtml' => xg_headline($this->titleHtml, array('count' => $this->totalCount, 'avatarUser' => $this->user)),
                'objects' => $this->groups,
                'numObjects' => $this->totalCount,
                'rowSize' => 2,
                'pageSize' => $this->pageSize,
                'sortOptions' => $this->hideSorts ? null : $this->pagePickerOptions,
                'searchUrl' => $this->_buildUrl('group', 'search'),
                'searchButtonText' => xg_html('SEARCH_GROUPS'),
				'itemCallback' => array($this, '_renderSearchResult'),
                'extraTemplateArgs' => is_null($this->groupIds) ? array() : $this->groupIds,
                'feedUrl' => null,
                'feedTitle' => null,
                'feedFormat' => null,
                'noObjectsSubtitle' => $this->noObjectsSubtitle,
                'noObjectsMessageHtml' => $this->noObjectsMessageHtml,
                'noObjectsLinkUrl' => $this->noObjectsLinkUrl,
                'noObjectsLinkText' => $this->noObjectsLinkText)); ?>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>