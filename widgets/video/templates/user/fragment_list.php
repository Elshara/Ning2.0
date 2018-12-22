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
                <p><%= xg_html('WE_COULD_NOT_FIND_ANYONE_MATCHING') %></p>
            </div>
        <?php
        } ?>
        <div class="vcards">
            <?php
            foreach ($users as $user) { ?>
                <dl class="vcard left">
                    <dt><%= Video_HtmlHelper::avatar($user->title, 54) %>  <%= Video_HtmlHelper::linkedScreenName($user->title); %></dt>
                    <dd>
                        <?php
                        if (Video_UserHelper::get($user, 'videoCount')) { ?>
                            <a href="<%= $this->_buildUrl('user', 'show', '?screenName=' . $user->title) %>"><%= xg_html('VIEW_VIDEOS') %></a>
                        <?php
                        } ?>
                    </dd>
                </dl>
            <?php
            } ?>
        </div>
        <?php
        if ($showViewAllPeopleLink) { ?>
            <p class="clear right"><a href="<%= $this->_buildUrl('user', 'list') %>"><%= xg_html('VIEW_POPULAR_CONTRIBUTORS') %>&nbsp;&#187;</a></p>
        <?php
        }
        $this->renderPartial('fragment_pagination', 'video', array('targetUrl' => $paginationUrl, 'pageParamName' => 'page', 'curPage' => $page, 'numPages' => $numPages)); ?>
    </div>
</div>

