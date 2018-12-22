<?php xg_header(W_Cache::current('W_Widget')->dir, xg_text('VIDEOS')); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
		<?php XG_PageHelper::subMenu(Video_HtmlHelper::subMenu($this->_widget)) ?>
        <?php
        XG_App::includeFileOnce('/lib/XG_ListTemplateHelper.php');
        XG_ListTemplateHelper::outputListPage(array(
                'cssInfix' => 'video',
                'featuredTitleText' => null,
                'featuredObjects' => null,
                'showViewAllFeaturedUrl' => false,
                'viewAllFeaturedUrl' => null,
				'titleHtml' => xg_headline(xg_text('SEARCH_RESULTS'),array(
					'count' => $this->numVideos,)),
                'objects' => $this->videos,
                'numObjects' => $this->numVideos,
                'rowSize' => 2,
                'pageSize' => $this->pageSize,
                'sortOptions' => null,
                'searchButtonText' => xg_html('SEARCH_VIDEOS'),
                'searchUrl' => $this->_buildUrl('video', 'search'),
                'extraTemplateArgs' => array(),
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