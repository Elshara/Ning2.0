<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle); ?>
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
					'count' => $this->numVideos,
					'avatarUser' => $this->user,)),
                'objects' => $this->videos,
                'numObjects' => $this->numVideos,
                'rowSize' => 2,
                'pageSize' => $this->pageSize,
                'sortOptions' => $this->sortOptions,
                'searchUrl' => $this->_buildUrl('video', 'search'),
                'searchButtonText' => xg_html('SEARCH_VIDEOS'),
                'extraTemplateArgs' => array(),
                'feedUrl' => $this->_buildUrl('video','listForContributor',array('screenName'=>$this->user->title,'rss'=>'true','xn_auth'=>'no')),
                'feedTitle' => xg_text('LATEST_VIDEOS'),
                'feedFormat' => 'rss',
                'noObjectsSubtitle' => $this->myOwnVideos ? xg_text('ADD_A_VIDEO') : '',
                'noObjectsMessageHtml' => $this->myOwnVideos ? xg_html('YOU_HAVE_NOT_ADDED_VIDEOS') : xg_html('X_HAS_NOT_ADDED_VIDEOS', xnhtmlentities(Video_FullNameHelper::fullName($this->user->title))),
				'noObjectsLinkUrl' => $this->myOwnVideos ? $this->_buildUrl('video', XG_MediaUploaderHelper::action()) : NULL,
				'noObjectsLinkText' => xg_text('ADD_A_VIDEO'))); ?>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>