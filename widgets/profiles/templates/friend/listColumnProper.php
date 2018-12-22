<?php
$this->renderPartial('fragment_membersNavigation', '_shared');
if ($this->myFriends) {
    XG_App::ningLoaderRequire('xg.profiles.embed.unfriend');
}
XG_App::includeFileOnce('/lib/XG_ListTemplateHelper.php');
XG_ListTemplateHelper::outputListPage(array(
        'cssInfix' => 'members',
        'featuredTitleText' => xg_text('FEATURED_MEMBERS'),
        'featuredObjects' => $this->featuredMembers,
        'isSortRandom' => $this->isSortRandom,
        'showViewAllFeaturedUrl' => $this->showViewAllFeaturedUrl,
        'viewAllFeaturedUrl' => $this->_buildUrl('friend', 'listFeatured'),
        'titleHtml' => $this->profile
        		? Profiles_HtmlHelper::friendsHeadline($this->pageTitle, $this->profile, $this->numUsers)
				: xg_headline($this->pageTitle, array('count' => $this->numUsers)) . XG_GroupHelper::groupLink(true),
        'objects' => $this->users,
        'numObjects' => $this->numUsers,
        'rowSize' => 3,
        'pageSize' => $this->pageSize,
        'sortOptions' => $this->sortOptions,
        'searchButtonText' => $this->searchButtonText,
        'searchUrl' => $this->searchUrl,
        'tabsHtml' => $this->tabsHtml,
        'extraTemplateArgs' => array('myFriends' => $this->myFriends, 'deleteUrl' => $this->_buildUrl('profile','unfriend',array('xn_out' => 'json'))),
        'feedUrl' => null,
        'feedTitle' => null,
        'feedFormat' => null,
        'noObjectsSubtitle' => null,
        'noObjectsMessageHtml' => $this->emptyMessage,
        'noObjectsShowInviteLink' => $this->showInviteLink,
        'noObjectsShowAddAsFriendLink' => $this->showAddAsFriendLink,
        'noObjectsAddAsFriendLinkHtml' => $this->addAsFriendLinkHtml,
        'noObjectsLinkUrl' => null,
        'noObjectsLinkText' => null,
        'paginationUrl' => $this->paginationUrl ? $this->paginationUrl : $this->_buildUrl('friend', 'list', array('user' => $_GET['user'], 'sort' => $_GET['sort'], 'page' => $_GET['page'], 'q' => $_GET['q']))
));
