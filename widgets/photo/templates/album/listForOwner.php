<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle);
if ($this->myOwnAlbums) {
	$titleHtml = xg_text('MY_ALBUMS');
    $noObjectsSubtitle = xg_text('ADD_ALBUMS');
    $noObjectsMessageHtml = xg_html('YOU_HAVENT_ADDED_ALBUMS');
    $noObjectsLinkUrl = $this->_buildUrl('album', 'new');
    $noObjectsLinkText = xg_text('ADD_ALBUMS');
} else {
	$titleHtml = xg_html('USER_ALBUMS', Photo_FullNameHelper::fullName($this->user->title));
    $noObjectsMessageHtml = xg_html('USER_HAS_NOT_ADDED_ALBUMS', xg_userlink(XG_Cache::profiles($this->user)));
} ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
		<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget,'album')) ?>
        <?php
        XG_App::includeFileOnce('/lib/XG_ListTemplateHelper.php');
        XG_ListTemplateHelper::outputListPage(array(
                'cssInfix' => 'albums',
                'featuredTitleText' => null,
                'featuredObjects' => null,
                'isSortRandom' => $this->isSortRandom,
                'showViewAllFeaturedUrl' => false,
                'viewAllFeaturedUrl' => null,
				'titleHtml' => xg_headline($titleHtml, array(
					'avatarUser' => $this->user, 'count' => $this->numAlbums)),
                'objects' => $this->albums,
                'numObjects' => $this->numAlbums,
                'rowSize' => 5,
                'pageSize' => $this->pageSize,
                'sortOptions' => $this->sortOptions,
                'searchButtonText' => xg_html('SEARCH_ALBUMS'),
                'searchUrl' => $this->_buildUrl('album', 'search'),
                'extraTemplateArgs' => array('showCreator' => false, 'coverPhotos' => $this->coverPhotos),
                'feedUrl' => null,
                'feedTitle' => null,
                'feedFormat' => null,
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
