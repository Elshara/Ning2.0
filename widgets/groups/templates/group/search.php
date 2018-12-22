<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle);
?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
		<?php XG_PageHelper::subMenu(Groups_HtmlHelper::subMenu($this->_widget)) ?>
        <?php
		XG_App::includeFileOnce('/lib/XG_ListTemplateHelper.php');
        XG_ListTemplateHelper::outputListPage(array(
                'cssInfix' => 'groups',
                'titleHtml' => xg_headline(xg_text('SEARCH_RESULTS'), array('count' => $this->totalCount)),
                'objects' => $this->groups,
                'numObjects' => $this->totalCount,
                'rowSize' => 2,
                'pageSize' => $this->pageSize,
                'sortOptions' => $this->hideSorts ? null : $this->pagePickerOptions,
                'searchUrl' => $this->_buildUrl('group', 'search'),
                'searchButtonText' => xg_html('SEARCH_GROUPS'),
				'itemCallback' => array($this, '_renderSearchResult'),
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