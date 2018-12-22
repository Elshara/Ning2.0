<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle);
if ($this->myOwnPhotos) {
	$titleHtml = xg_text('MY_PHOTOS');
    $noObjectsSubtitle = xg_text('ADD_PHOTOS');
    $noObjectsMessageHtml = xg_html('YOU_HAVENT_ADDED_PHOTOS');
    $noObjectsLinkUrl = $this->_buildUrl('photo', XG_MediaUploaderHelper::action());
    $noObjectsLinkText = xg_text('ADD_PHOTOS');
} else {
	$titleHtml = xg_html('USER_PHOTOS', Photo_FullNameHelper::fullName($this->user->title));
    $noObjectsMessageHtml = xg_html('USER_HAS_NOT_ADDED_PHOTOS', xg_userlink(XG_Cache::profiles($this->user)));
}
?>
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
				'titleHtml' => xg_headline($titleHtml,array(
					'count' => $this->numPhotos,
					'avatarUser' => $this->user,
					'byline2Html' => $this->numPhotos ? Photo_HtmlHelper::slideshowLink(array('feed_url' => Photo_SlideshowHelper::feedUrl('for_contributor', $this->user->title))) : NULL,
				)),
                'objects' => $this->photos,
                'numObjects' => $this->numPhotos,
                'rowSize' => 5,
                'pageSize' => $this->pageSize,
                'sortOptions' => $this->sortOptions,
                'searchButtonText' => xg_html('SEARCH_PHOTOS'),
                'searchUrl' => $this->_buildUrl('photo', 'search'),
                'extraTemplateArgs' => array('showCreator' => false, 'context' => 'user'),
                'feedUrl' => $this->_buildUrl('photo', 'listForContributor', array('screenName' => $this->user->title, 'rss' => 'yes', 'xn_auth' => 'no')),
                'feedTitle' => xg_text('XS_PHOTOS', Photo_FullNameHelper::fullName($this->user->title)),
                'feedFormat' => 'rss',
                'noObjectsSubtitle' => $noObjectsSubtitle,
                'noObjectsMessageHtml' => $noObjectsMessageHtml,
                'noObjectsLinkUrl' => $noObjectsLinkUrl,
				'noObjectsLinkText' => $noObjectsLinkText)); ?>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>