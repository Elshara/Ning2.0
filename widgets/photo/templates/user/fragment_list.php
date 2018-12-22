<?php
/**
 * Renders the grid view of a list of people
 *
 * @param $searchLabel  The label for the search form at the top of the page
 * @param $screenName (optional)  Optional screen name for friends searching
 * @param $showViewAllPeopleLink
 * @param $paginationUrl  Target base URL for search form and pagination links
 * @param $sort  How to sort results
 * @param $page  Which page of results is being displayed
 * @param $numPages  Total number of pages in result set
 * @param $searchFor  Optional search term
 * @param $users  Array of users to display
 */ ?>


 <?php XG_PageHelper::searchBar(array(
 	'url' => xnhtmlentities($paginationUrl),
 	'buttonText' => xg_html('SEARCH_FRIENDS'),
 ))?>
<div class="xg_module">
    <div class="xg_module_body">
        <?php
        if (count($users) == 0) { ?>
            <div>
                <p><%= isset($searchFor) ? xg_html('WE_COULD_NOT_FIND_ANYONE') : xg_html('WE_COULD_NOT_FIND_ANYONE_MATCHING') %>
            </div>
        <?php
        } ?>
        <div class="vcards">
            <?php
            foreach ($users as $user) {
                if (Photo_UserHelper::get($user, 'photoCount')>0) { ?>
                <dl class="vcard left">
                    <dt><%= Photo_HtmlHelper::avatar($user->title, 54) %>  <%= Photo_HtmlHelper::linkedScreenName($user->title); %></dt>
                    <dd><a href="<%= $this->_buildUrl('user', 'show', '?screenName=' . $user->title) %>"><%= xg_html('VIEW_PHOTOS') %></a></dd>
                </dl>
                <?php } ?>
            <?php
            } ?>
        </div>
        <?php
        if ($showViewAllPeopleLink) { ?>
            <p class="clear right"><a href="<%= $this->_buildUrl('user', 'list') %>"><%= xg_html('VIEW_POPULAR_CONTRIBUTORS') %>&nbsp;&#187;</a></p>
        <?php
        }
        $this->renderPartial('fragment_pagination', 'photo', array('targetUrl' => $paginationUrl, 'pageParamName' => 'page', 'curPage' => $page, 'numPages' => $numPages)); ?>
    </div>
</div>
