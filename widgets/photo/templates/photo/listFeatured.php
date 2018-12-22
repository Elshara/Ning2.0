<?php xg_header(W_Cache::current('W_Widget')->dir, xg_text('PHOTOS')); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
		<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget)) ?>
        <?php
        XG_App::includeFileOnce('/lib/XG_ListTemplateHelper.php');
        XG_ListTemplateHelper::outputListPage(array(
                'cssInfix' => 'photo',
                'featuredTitleText' => null,
                'featuredObjects' => null,
                'isSortRandom' => $this->isSortRandom,
                'showViewAllFeaturedUrl' => false,
                'viewAllFeaturedUrl' => null,
				'titleHtml' => xg_headline(xg_text('FEATURED_PHOTOS'),array(
					'count' => $this->numPhotos,
					'byline2Html' => $this->numPhotos ? Photo_HtmlHelper::slideshowLink(array('feed_url' => Photo_SlideshowHelper::feedUrl('promoted'))) : NULL)),
                'objects' => $this->photos,
                'numObjects' => $this->numPhotos,
                'rowSize' => 5,
                'pageSize' => $this->pageSize,
                'sortOptions' => null,
                'searchButtonText' => xg_html('SEARCH_PHOTOS'),
                'searchUrl' => $this->_buildUrl('photo', 'search'),
                'extraTemplateArgs' => array('showCreator' => true, 'context' => 'featured'),
                'feedUrl' => null,
                'feedTitle' => null,
                'feedFormat' => null,
                'noObjectsSubtitle' => null,
                'noObjectsMessageHtml' => xg_html('NO_FEATURED_PHOTOS'),
                'noObjectsLinkUrl' => null,
				'noObjectsLinkText' => null)); ?>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
