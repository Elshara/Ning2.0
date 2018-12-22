<?php
XG_IPhoneHelper::header('featured', $this->pageTitle, $this->profile, NULL);
XG_IPhoneHelper::outputListPage(array(
        'cssInfix' => 'members',
        'featuredTitleText' => null,
        'featuredObjects' => null,
        'showViewAllFeaturedUrl' => null,
        'viewAllFeaturedUrl' => null,
        'titleHtml' => xnhtmlentities($this->pageTitle),
        'objects' => $this->users,
        'numObjects' => $this->numUsers,
        'rowSize' => 3,
        'pageSize' => $this->pageSize,
        'sortOptions' => $this->sortOptions,
        'searchUrl' => $this->searchUrl,
        'searchButtonText' => xg_html('SEARCH_MEMBERS'),
        'extraTemplateArgs' => array(),
        'feedUrl' => null,
        'feedTitle' => null,
        'feedFormat' => null,
        'noObjectsSubtitle' => null,
        'noObjectsMessageHtml' => null,
        'noObjectsLinkUrl' => null,
        'noObjectsLinkText' => null,
		'showNextLink' => $this->showNextLink
)); ?>
<ul>
	<li class="add"><a href="<%=qh(W_Cache::getWidget('main')->buildUrl('invitation', 'new', array('previousUrl' => XG_HttpHelper::currentUrl())))%>"><%= xg_html('INVITE_MORE_PEOPLE') %></a></li>
</ul>
<?php xg_footer(); ?>
