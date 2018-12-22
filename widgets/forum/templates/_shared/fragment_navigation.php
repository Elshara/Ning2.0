<?php
/**
 * The Forum-specific links at the top of the page.
 *
 * @param $categoryId string  ID of the initial category for new discussions (optional)
 * @param $hideStartDiscussionLink boolean  Whether to hide the "Start a New Discussion" link
 */
$navLinks = !XG_GroupHelper::inGroupContext();
$this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
$addLink = Forum_SecurityHelper::currentUserCanSeeAddTopicLinks() && ! $hideStartDiscussionLink;
if ($navLinks || $addLink) {
?>
<ul class="navigation">
    <?php if($navLinks) { ?>
        <li><a href="<%= xnhtmlentities($this->_buildUrl('index', 'index')) %>"><%= xg_html('ALL_DISCUSSIONS2') %></a></li>
        <li><a href="<%= xnhtmlentities($this->_buildUrl('topic', 'listForContributor', array('user' => XN_Profile::current()->screenName))) %>"><%= xg_html('MY_DISCUSSIONS') %></a></li>
    <?php }
    if ($addLink) { ?>
        <li class="right"><a <%= XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl($categoryId)) %> class="desc add"><%= xg_html('ADD_A_DISCUSSION') %></a></li>
    <?php
    } ?>
</ul>
<?php } ?>
