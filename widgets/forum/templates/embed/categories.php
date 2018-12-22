<?php 
$this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
$showCreateLink = ($this->embed->getType() != 'profiles' || $this->embed->isOwnedByCurrentUser()) && Forum_SecurityHelper::currentUserCanSeeAddTopicLinks();
if ($this->embed->get('itemCount') == 0 && $this->userCanEdit) { ?>
    <div class="xg_module_foot">
        <ul>
        <?php
        if ($showCreateLink) { ?>
            <li class="left"><a <%= XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl()) %> class="desc add"><%= xg_html('START_DISCUSSION') %></a></li>
        <?php
        } ?>
        </ul>
    </div>
<?php
} else{ ?>
    <div class="xg_module_body">
    <?php
        foreach($this->categories as $category) { 
            $href = $this->_buildUrl('topic', 'listForCategory', array('categoryId' => $category->id));?>
            <div class="category <%= $count == 0 ? 'last-child11' : '' %>">
                <h3><a href="<%= $href %>"><%= xnhtmlentities($category->title) %></a></h3>
                <p class="small"><%= xnhtmlentities($category->description) %></p>
                <p class="small"><%= xg_html('X_DISCUSSIONS', $category->my->discussionCount) %></p>
            </div>
        <?php }
    ?>
    </div>
    <?php if ($this->showViewAll || $showCreateLink) { ?>
        <div class="xg_module_foot">
            <ul>
                <?php if ($showCreateLink) { ?>
                    <li class="left"><a <%= XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl()) %> class="desc add"><%= xg_html('START_DISCUSSION') %></a></li>
                <?php } ?>
        <?php if ($this->showViewAll) { ?>
                    <li class="right"><a href="<%= $this->_buildUrl('category', 'listByTitle') %>"><%= xg_html('VIEW_ALL') %></a></li>
                <?php } ?>
            </ul>
            </div>
        <?php } ?>
<?php } ?>