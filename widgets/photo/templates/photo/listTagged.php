<?php xg_header(W_Cache::current('W_Widget')->dir, xg_text('PHOTOS')); ?>
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
				'titleHtml' => xg_headline(xg_text('ALL_PHOTOS_TAGGED_X', $this->tag), array('count' => $this->numPhotos)),
                'objects' => $this->photos,
                'numObjects' => $this->numPhotos,
                'rowSize' => 5,
                'pageSize' => $this->pageSize,
                'sortOptions' => $this->sortOptions,
                'searchButtonText' => xg_html('SEARCH_PHOTOS'),
                'searchUrl' => $this->_buildUrl('photo', 'search'),
                'extraTemplateArgs' => array('showCreator' => true, 'context' => null),
                'feedUrl' => $this->_buildUrl('photo', 'listTagged', '?tag=' . urlencode($this->tag) . '&rss=yes&xn_auth=no'),
                'feedTitle' => $title,
                'feedFormat' => 'rss',
                'noObjectsSubtitle' => xg_text('ADD_PHOTOS'),
                'noObjectsMessageHtml' => xg_html('NO_PHOTOS_TAGGED_X_CHECK', xnhtmlentities($this->tag), 'href="' . W_Cache::getWidget('forum')->buildUrl('topic', 'listForTag', array('tag' => $this->tag)) . '"', 'href="' . W_Cache::getWidget('video')->buildUrl('video', 'listTagged', array('tag' => $this->tag)) . '"', 'href="' . xnhtmlentities(W_Cache::getWidget('profiles')->buildUrl('blog', 'list', array('tag' => $_GET['tag']))) . '"'),
                'noObjectsLinkUrl' => $this->_buildUrl('photo', XG_MediaUploaderHelper::action()),
				'noObjectsLinkText' => xg_text('ADD_PHOTOS'))); ?>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
