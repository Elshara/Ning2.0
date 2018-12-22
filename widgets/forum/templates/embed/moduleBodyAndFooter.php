<?php
$this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
$showCreateLink = ($this->embed->getType() != 'profiles' || $this->userCanEdit) && Forum_SecurityHelper::currentUserCanSeeAddTopicLinks();
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
} else {
    if (count($this->topicsAndComments)) { ?>
        <div class="xg_module_body">
            <?php
            foreach($this->topicsAndComments as $topicOrComment) {
                $comment = $topicOrComment->type == 'Comment' ? $topicOrComment : NULL;
                $topic = $topicOrComment->type == 'Comment' ? $this->topics[$topicOrComment->my->attachedTo] : $topicOrComment;
                $categoryLink = $this->categoryIds ? array('href="' . xnhtmlentities($this->_buildUrl('topic', 'listForCategory', array('categoryId' => $topic->my->categoryId))) . '"', xnhtmlentities($this->categoryIds[$topic->my->categoryId]->title)) : null;
                ?>
                    <?php $this->renderPartial('fragment_topic', '_shared', array(
                            'topic' => $topic, 'comment' => $comment,
                            'showListForContributorLink' => FALSE,
                            'showContributorName' => $this->showContributorName,
                            'showAvatar' => $this->embed->getType() != 'profiles',
                            'avatarSize' => 32,
                            'lineBreakAfterTitle' => $this->embed->getType() == 'profiles',
                            'categoryLink' => $categoryLink,
                            'showExcerptAndTags' => $this->embed->get('displaySet') !== 'titles')); ?>
        <?php
        } ?>
        </div>
    <?php
    }
    if (! $this->topicsAndComments && $this->embed->isOwnedByCurrentUser() && $this->embed->getType() == 'profiles' && Forum_SecurityHelper::currentUserCanSeeAddTopicLinks()) { ?>
        <div class="xg_module_body">
            <h3><%= xg_html('YOU_HAVE_NOT_ADDED_DISCUSSIONS_2') %></h3>
            <p><%= xg_html('ADD_DISCUSSIONS_AND_SHARE') %></p>
            <p><a <%= XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl()) %> class="desc add"><%= xg_html('START_A_DISCUSSION') %></a></p>
        </div>
    <?php
    } elseif (! $this->topicsAndComments && $this->userCanEdit && $this->embed->get('topicSet') == 'promoted') { ?>
        <div class="xg_module_body">
            <h3><%= xg_html('THERE_ARE_NO_FEATURED_X', mb_strtolower(xg_html('DISCUSSIONS'))) %></h3>
            <p><%= xg_html('START_FEATURING_X_CLICK_Y', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildRelativeUrl('admin','featuring')) .'"') %></p>
        </div>
    <?php
    } elseif (! $this->topicsAndComments && $this->userCanEdit && $this->embed->get('topicSet') == 'ownerDiscussions') { ?>
        <div class="xg_module_body">
            <h3><%= xg_html('THERE_ARE_NO_X_THAT_MATCH_SETTINGS', mb_strtolower(xg_html('DISCUSSIONS'))) %></h3>
            <p><%= xg_html('CLICK_EDIT_AND_CHANGE_SETTINGS', mb_strtolower(xg_html('DISCUSSIONS')), 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildRelativeUrl('admin','customization','#a1-11')) .'"') %></p>
        </div>
    <?php
    } elseif (! $this->topicsAndComments && $this->userCanEdit && preg_match('/category_(.+)/u', $this->embed->get('topicSet'))) { ?>
        <div class="xg_module_body">
            <h3><%= xg_html('THERE_ARE_NO_X_THAT_MATCH_SETTINGS', mb_strtolower(xg_html('DISCUSSIONS'))) %></h3>
            <p><%= xg_html('CLICK_EDIT_AND_CHANGE_SETTINGS', mb_strtolower(xg_html('DISCUSSIONS')), 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildRelativeUrl('admin','customization','#a1-11')) .'"') %></p>
        </div>
    <?php
    // On the Groups page, we always display something, even if the current user is not the embed owner.
    // Otherwise the center column of the page will be blank. [Jon Aquino 2007-05-07]
    } elseif (! $this->topicsAndComments && $this->embed->getType() == 'groups' && ! $this->groupHasTopics && Forum_SecurityHelper::currentUserCanSeeAddTopicLinks()) { ?>
        <div class="xg_module_body">
            <h3><%= xg_html('START_A_DISCUSSION') %></h3>
            <p><%= xg_html('NOBODY_HAS_ADDED_DISCUSSIONS_ADD') %></p>
            <p><a <%= XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl()) %> class="desc add"><%= xg_html('START_A_DISCUSSION') %></a></p>
        </div>
    <?php
    } elseif (! $this->topicsAndComments && $this->embed->getType() == 'groups' && ! $this->groupHasTopics) { ?>
        <div class="xg_module_body">
            <p><%= xg_html('GROUP_HAS_NO_DISCUSSIONS') %></p>
        </div>
    <?php
    } elseif (! $this->topicsAndComments && $this->embed->getType() == 'groups' && $this->groupHasTopics) { ?>
        <div class="xg_module_body">
            <p><%= xg_html('VIEW_DISCUSSIONS_IN_GROUP_FORUM', 'href="' . xnhtmlentities($this->_buildUrl('index', 'index')) . '"', xnhtmlentities(XG_GroupHelper::currentGroup()->title)) %></p>
        </div>
    <?php
    } elseif (! $this->topicsAndComments && $this->userCanEdit && Forum_SecurityHelper::currentUserCanSeeAddTopicLinks()) { ?>
        <div class="xg_module_body">
            <p><a <%= XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl()) %> class="desc add"><%= xg_html('START_A_DISCUSSION') %></a></p>
        </div>
    <?php
    } elseif ($this->topicsAndComments) {
        $moreUrl = $this->embed->getType() == 'profiles' ? $this->_buildUrl('topic', 'listForContributor', array('user' => $this->embed->getOwnerName())) : $this->_buildUrl('index', 'index');
        $feedBase = $this->embed->getType() == 'profiles' ? $this->_buildUrl('topic', 'listForContributor', array('user' => $this->embed->getOwnerName())) : $this->_buildUrl('topic', 'list');
        $feedUrl = XG_HttpHelper::addParameters($feedBase, array('feed' => 'yes', 'xn_auth' => 'no'));
        if ($this->feedAutoDiscoveryTitle) { xg_autodiscovery_link($feedUrl, $this->feedAutoDiscoveryTitle, 'atom'); }
        ?>
        <div class="xg_module_foot">
            <ul>
                <?php
                if (($showCreateLink || $this->embed->getType() == 'groups') && Forum_SecurityHelper::currentUserCanAddTopic()) { ?>
                    <li class="left"><a <%= XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl()) %> class="desc add"><%= xg_html('START_DISCUSSION') %></a></li>
                <?php
                } ?>
                <li class="right"><a href="<%= xnhtmlentities($moreUrl) %>"><%= xg_html('VIEW_ALL') %></a></li>
            </ul>
        </div>
    <?php
    }
} ?>
