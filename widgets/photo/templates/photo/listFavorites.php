<?php
xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle);
if ($this->myOwnFavorites) {
	$titleHtml = xg_text('MY_FAVORITES');
    $noObjectsSubtitle = xg_text('ADD_FAVORITES');
    $noObjectsMessageHtml = xg_html('YOU_CAN_MARK_PHOTO');
    $noObjectsLinkUrl = $this->_buildUrl('photo', 'list');
    $noObjectsLinkText = xg_text('VIEW_RECENT_PHOTOS');
} else {
	$titleHtml = xg_text('XS_FAVORITES', Photo_FullNameHelper::fullName($this->user->title));
    $noObjectsMessageHtml = xg_html('X_HAS_NOT_ADDED_FAVORITES', xg_userlink(XG_Cache::profiles($this->user)));
} ?>
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
					'byline2Html' => $this->numPhotos ? Photo_HtmlHelper::slideshowLink(array('owner' => $this->user->title, 'favorites' => true, 'sort' => $_GET['sort'])) : NULL)),
                'objects' => $this->photos,
                'numObjects' => $this->numPhotos,
                'rowSize' => 5,
                'pageSize' => $this->pageSize,
                'sortOptions' => $this->sortOptions,
                'searchButtonText' => xg_html('SEARCH_PHOTOS'),
                'searchUrl' => $this->_buildUrl('photo', 'search'),
                'extraTemplateArgs' => array('showCreator' => true, 'context' => null),
                'feedUrl' => $this->_buildUrl('photo','listFavorites', '?screenName=' . $this->user->title . '&rss=yes&xn_auth=no'),
                'feedTitle' => xg_text('XS_FAVORITE_PHOTOS_ON_X', Photo_FullNameHelper::fullName($this->user->title), XN_Application::load()->name),
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