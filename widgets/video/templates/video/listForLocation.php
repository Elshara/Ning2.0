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
                'isSortRandom' => $this->isSortRandom,
                'showViewAllFeaturedUrl' => false,
                'viewAllFeaturedUrl' => null,
				'titleHtml' => xg_headline(xg_text('VIDEOS_FOR_LOCATION', $this->location),array(
					'count' => $this->numPhotos,)),
                'objects' => $this->videos,
                'numObjects' => $this->numVideos,
                'rowSize' => 2,
                'pageSize' => $this->pageSize,
                'sortOptions' => $this->sortOptions,
                'searchUrl' => $this->_buildUrl('video', 'search'),
                'searchButtonText' => xg_html('SEARCH_VIDEOS'),
                'extraTemplateArgs' => array(),
                'feedUrl' => $this->_buildUrl('video','listForLocation',array('rss'=>'yes','xn_auth'=>'no','location'=>$this->location)),
                'feedTitle' => xg_text('VIDEOS_FOR_LOCATION', $this->location),
                'feedFormat' => 'rss',
                'noObjectsSubtitle' => xg_text('ADD_VIDEOS'),
                'noObjectsMessageHtml' => xg_html('NO_VIDEOS_FOR_LOCATION', xnhtmlentities($this->location)),
                'noObjectsLinkUrl' => $this->_buildUrl('video', XG_MediaUploaderHelper::action()),
				'noObjectsLinkText' => xg_text('ADD_VIDEOS'))); ?>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
