<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle, null, array('showFacebookMeta' => $this->showFacebookMeta)); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
		<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget), $this->subMenuItem) ?>
		<?php
        XG_App::includeFileOnce('/lib/XG_ListTemplateHelper.php');
        XG_ListTemplateHelper::outputListPage(array(
                'cssInfix' => 'photo',
                'featuredTitleText' => xg_text('FEATURED_PHOTOS'),
                'featuredObjects' => $this->featuredPhotos,
                'isSortRandom' => $this->isSortRandom,
                'showViewAllFeaturedUrl' => $this->showViewAllFeaturedUrl,
                'viewAllFeaturedUrl' => $this->_buildUrl('photo', 'listFeatured'),
				'titleHtml' => xg_headline($this->title, array('count' => $this->numPhotos, 'byline2Html' => $this->slideshowLink)),
                'objects' => $this->photos,
                'numObjects' => $this->numPhotos,
                'rowSize' => 5,
                'pageSize' => $this->pageSize,
                'sortOptions' => $this->sortOptions,
                'searchButtonText' => xg_html('SEARCH_PHOTOS'),
                'searchUrl' => $this->_buildUrl('photo', 'search'),
                'extraTemplateArgs' => array('showCreator' => true, 'context' => null),
                'feedUrl' => $this->_buildUrl('photo','rss','?xn_auth=no'),
                'feedTitle' => xg_text('LATEST_PHOTOS'),
                'feedFormat' => 'rss',
                'noObjectsSubtitle' => xg_text('ADD_PHOTOS'),
                'noObjectsMessageHtml' => xg_html('NOBODY_HAS_ADDED_PHOTOS'),
                'noObjectsLinkUrl' => $this->_buildUrl('photo', XG_MediaUploaderHelper::action()),
                'noObjectsLinkText' => xg_text('ADD_PHOTOS'))); ?>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
