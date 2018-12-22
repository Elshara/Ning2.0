<?php
/**
 * The body and footer of the group-list module, which displays recent or popular groups on the homepage and profile page.
 *
 * @param $groups array  The Group objects to display
 * @param $embed XG_Embed  Stores the module data.
 * @param $showViewAllLink boolean  Whether to show the "View All" link in the footer
 * @param $columnCount integer  the number of columns that the module spans
 */
$this->_widget->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php');
$showCreateLink = $embed->isOwnedByCurrentUser() && Groups_SecurityHelper::currentUserCanSeeCreateGroupLinks();
if ($embed->get('itemCount') == 0 && $embed->isOwnedByCurrentUser()) { ?>
    <div class="xg_module_foot">
        <ul>
            <?php
            if ($showCreateLink) { ?>
                <li class="left"><a class="desc add" <%= XG_JoinPromptHelper::promptToJoin($this->_buildUrl('group', 'new')) %>><%= xg_html('ADD_A_GROUP') %></a></li>
            <?php
            } ?>
        </ul>
    </div>
<?php
} else{
    if ($groups) { ?>
        <div class="xg_module_body body_list">
            <?php
            $totalGroups = count($groups);
            $count = 1;
            foreach(array_chunk($groups, $columnCount == 1 ? 2 : 5) as $chunk) { ?>
                <ul class="clist">
                    <?php
                    foreach($chunk as $group) {
                         $this->renderPartial('fragment_miniGroup', '_shared', array('group' => $group, 'avatarWidth' => $columnCount == 1 ? 40: 82, 'showCreatedBy' => true, 'lastChild' => $totalGroups == $count));
                         $count ++;
                    } ?>
                </ul>
            <?php
            } ?>
          </ul>
        </div>
    <?php
    }
    if (! $groups && $embed->getType() == 'profiles' && $embed->isOwnedByCurrentUser() && Groups_SecurityHelper::currentUserCanSeeCreateGroupLinks()) { ?>
        <div class="xg_module_body">
            <h3><%= xg_html('YOU_HAVE_NOT_CREATED_GROUPS_2') %></h3>
            <p><%= xg_html('CREATE_GROUPS_AND_SHARE') %></p>
            <p><a <%= XG_JoinPromptHelper::promptToJoin($this->_buildUrl('group', 'new')) %> class="desc add"><%= xg_html('ADD_A_GROUP') %></a></p>
        </div>
    <?php
    } elseif (! $groups && $embed->get('groupSet') != 'promoted' && Groups_SecurityHelper::currentUserCanSeeCreateGroupLinks()) { ?>
        <div class="xg_module_body">
            <p><a <%= XG_JoinPromptHelper::promptToJoin($this->_buildUrl('group', 'new')) %> class="desc add"><%= xg_html('ADD_A_GROUP') %></a></p>
        </div>
    <?php
    } elseif (! $groups && $embed->get('groupSet') == 'promoted' && $embed->isOwnedByCurrentUser()) { ?>
        <div class="xg_module_body">
            <h3><%= xg_html('THERE_ARE_NO_FEATURED_GROUPS') %></h3>
            <p><%= xg_html('START_FEATURING_X_CLICK_Y', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildRelativeUrl('admin','featuring')) .'"') %></p>
        </div>
    <?php
    } elseif ($groups) {
        $moreUrl = $embed->getType() == 'profiles' ? $this->_buildUrl('group', 'listForContributor', array('user' => $embed->getOwnerName())) : $this->_buildUrl('index', 'index');
        if ($showViewAllLink || $showCreateLink) { ?>
            <div class="xg_module_foot">
                <ul>
                    <?php
                    if ($showCreateLink) { ?>
                        <li class="left"><a class="desc add" <%= XG_JoinPromptHelper::promptToJoin($this->_buildUrl('group', 'new')) %>><%= xg_html('ADD_A_GROUP') %></a></li>
                    <?php
                    } ?>
                    <?php
                    if ($showViewAllLink) { ?>
                        <li class="right"><a href="<%= xnhtmlentities($moreUrl) %>"><%= xg_html('VIEW_ALL') %></a></li>
                    <?php
                    } ?>
                </ul>
            </div>
        <?php
        }
    }
} ?>