<?php xg_header(W_Cache::current('W_Widget')->dir, xg_text('ALBUMS_NO_COLON')); ?>
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
                'showViewAllFeaturedUrl' => null,
                'viewAllFeaturedUrl' => null,
                'titleHtml' => xg_headline($this->photo->title ? xg_text('ALBUMS_WITH_X', $this->photo->title) : xg_text('ALBUMS_WITH_THIS_PHOTO'),
                	array('count' => $this->numAlbums)),
                'objects' => $this->albums,
                'numObjects' => $this->numAlbums,
                'rowSize' => 5,
                'pageSize' => $this->pageSize,
                'sortOptions' => $this->sortOptions,
                'searchButtonText' => xg_html('SEARCH_ALBUMS'),
                'searchUrl' => $this->_buildUrl('album', 'search'),
                'extraTemplateArgs' => array('showCreator' => true, 'coverPhotos' => $this->coverPhotos),
                'feedUrl' => null,
                'feedTitle' => null,
                'feedFormat' => null,
                'noObjectsSubtitle' => xg_text('ADD_AN_ALBUM'),
                'noObjectsMessageHtml' => xg_html('THERE_ARE_NO_ALBUMS_CONTAINING_PHOTO'),
                'noObjectsLinkUrl' => $this->_buildUrl('album', 'new'),
                'noObjectsLinkText' => xg_text('ADD_AN_ALBUM'))); ?>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
