<?php xg_header(W_Cache::current('W_Widget')->dir, xg_text('ALBUMS_NO_COLON')); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
		<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget,'album')) ?>
        <?php
        XG_App::includeFileOnce('/lib/XG_ListTemplateHelper.php');
        XG_ListTemplateHelper::outputListPage(array(
                'cssInfix' => 'albums',
                'featuredTitleText' => null,
                'featuredObjects' => null,
                'showViewAllFeaturedUrl' => false,
                'viewAllFeaturedUrl' => null,
				'titleHtml' => xg_headline(xg_text('SEARCH_RESULTS'),array(
					'count' => $this->numAlbums,)),
                'objects' => $this->albums,
                'numObjects' => $this->numAlbums,
                'rowSize' => 5,
                'pageSize' => $this->pageSize,
                'sortOptions' => null,
                'searchButtonText' => xg_html('SEARCH_ALBUMS'),
                'searchUrl' => $this->_buildUrl('album', 'search'),
                'extraTemplateArgs' => array('showCreator' => true, 'coverPhotos' => $this->coverPhotos),
                'feedUrl' => null,
                'feedTitle' => null,
                'feedFormat' => null,
                'noObjectsSubtitle' => null,
                'noObjectsMessageHtml' => null,
                'noObjectsLinkUrl' => null,
				'noObjectsLinkText' => null)); ?>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
