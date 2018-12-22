<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
		<?php XG_PageHelper::subMenu(Video_HtmlHelper::subMenu($this->_widget)) ?>
        <?php
        XG_App::includeFileOnce('/lib/XG_ListTemplateHelper.php');
        XG_ListTemplateHelper::outputListPage(array(
                'cssInfix' => 'video',
                'featuredTitleText' => null,
                'featuredObjects' => null,
                'isSortRandom' => $this->isSortRandom,
                'showViewAllFeaturedUrl' => false,
                'viewAllFeaturedUrl' => null,
				'titleHtml' => xg_headline($this->title, array(
					'count' => $this->numVideos,)),
                'objects' => $this->videos,
                'numObjects' => $this->numVideos,
                'rowSize' => 2,
                'pageSize' => $this->pageSize,
                'sortOptions' => null,
                'searchUrl' => $this->_buildUrl('video', 'search'),
                'searchButtonText' => xg_html('SEARCH_VIDEOS'),
                'extraTemplateArgs' => array(),
                'feedUrl' => null,
                'feedTitle' => null,
                'feedFormat' => null,
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
