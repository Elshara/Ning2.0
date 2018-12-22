<?php
XG_IPhoneHelper::outputListPage(array(
        'cssInfix' => 'members',
        'featuredTitleText' => xg_text('FEATURED_MEMBERS'),
        'featuredObjects' => $this->featuredMembers,
        'showViewAllFeaturedUrl' => $this->showViewAllFeaturedUrl,
        'viewAllFeaturedUrl' => $this->_buildUrl('friend', 'listFeatured'),
        'titleHtml' => xnhtmlentities($this->pageTitle),
        'objects' => $this->users,
        'numObjects' => $this->numUsers,
        'rowSize' => 1,
        'pageSize' => $this->pageSize,
        'sortOptions' => $this->sortOptions,
        'searchButtonText' => $this->searchButtonText,
        'searchUrl' => $this->searchUrl,
        'extraTemplateArgs' => array(),
        'feedUrl' => null,
        'feedTitle' => null,
        'feedFormat' => null,
        'noObjectsSubtitle' => null,
        'noObjectsMessageHtml' => $this->emptyMessage,
        'noObjectsLinkUrl' => null,
        'noObjectsLinkText' => null,
		'showNextLink' => $this->showNextLink
)); ?>