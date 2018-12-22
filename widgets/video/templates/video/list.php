<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle, null, array('showFacebookMeta' => $this->showFacebookMeta)); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
		<?php XG_PageHelper::subMenu(Video_HtmlHelper::subMenu($this->_widget)) ?>
        <?php
        XG_App::includeFileOnce('/lib/XG_ListTemplateHelper.php');
        XG_ListTemplateHelper::outputListPage(array(
                'cssInfix' => 'video',
                'featuredTitleText' => xg_text('FEATURED_VIDEOS'),
                'featuredObjects' => $this->featuredVideos,
                'isSortRandom' => $this->isSortRandom,
                'showViewAllFeaturedUrl' => $this->showViewAllFeaturedUrl,
                'viewAllFeaturedUrl' => $this->_buildUrl('video', 'listFeatured'),
				'titleHtml' => xg_headline($this->title, array(
					'count' => $this->numVideos,)),
                'objects' => $this->videos,
                'numObjects' => $this->numVideos,
                'rowSize' => 2,
                'pageSize' => $this->pageSize,
                'sortOptions' => $this->sortOptions,
                'searchUrl' => $this->_buildUrl('video', 'search'),
                'searchButtonText' => xg_html('SEARCH_VIDEOS'),
                'extraTemplateArgs' => array(),
                'feedUrl' => $this->_buildUrl('video','rss','?xn_auth=no'),
                'feedTitle' => xg_text('LATEST_VIDEOS'),
                'feedFormat' => 'rss',
                'noObjectsSubtitle' => xg_text('ADD_A_VIDEO'),
                'noObjectsMessageHtml' => xg_html('NOBODY_HAS_ADDED_VIDEOS'),
                'noObjectsLinkUrl' => $this->_buildUrl('video', XG_MediaUploaderHelper::action()),
                'noObjectsLinkText' => xg_text('ADD_A_VIDEO'))); ?>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
