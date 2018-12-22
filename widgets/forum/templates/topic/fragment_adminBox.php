<?php
/**
 * Displays a box containing link for administrative tasks.
 */
$adminOptionListItems = array();
if (! XG_GroupHelper::inGroupContext()) {
    XG_App::includeFileOnce('/lib/XG_PromotionHelper.php');
    ob_start();
    W_Cache::getWidget('main')->dispatch('promotion','link',array($this->topic));
    $featureLink = trim(ob_get_contents());
    ob_end_clean();
    if ($featureLink) { $adminOptionListItems[] = '<li>' . $featureLink . '</li>'; }
}
if (Forum_SecurityHelper::currentUserCanEditTopic($this->topic)) {
    ob_start(); ?>
    <li><a <%= XG_JoinPromptHelper::promptToJoin($this->_buildUrl('topic', 'edit', array('id' => $this->topic->id))) %> class="desc edit"><%= xg_html('EDIT_DISCUSSION') %></a></li>
    <?php
    $adminOptionListItems[] = ob_get_contents();
    ob_end_clean();
}
if (Forum_SecurityHelper::currentUserCanCloseComments($this->topic)) {
    XG_App::ningLoaderRequire('xg.shared.PostLink');
    ob_start(); ?>
    <li>
        <a href="#" dojoType="PostLink"
            _url="<%= xnhtmlentities($this->_buildUrl('topic', 'closeComments', array('id' => $this->topic->id, 'target' => XG_HttpHelper::currentUrl()))) %>"
            class="desc close-discussion"
            title="<%= xg_html('CLOSING_DISCUSSION_PREVENTS') %>">
                <%= xg_html('CLOSE_DISCUSSION') %>
        </a>
    </li>
    <?php
    $adminOptionListItems[] = ob_get_contents();
    ob_end_clean();
}
if (Forum_SecurityHelper::currentUserCanOpenComments($this->topic)) {
    XG_App::ningLoaderRequire('xg.shared.PostLink');
    ob_start(); ?>
    <li>
        <a href="#" dojoType="PostLink"
            _url="<%= xnhtmlentities($this->_buildUrl('topic', 'openComments', array('id' => $this->topic->id, 'target' => XG_HttpHelper::currentUrl()))) %>"
            class="desc open-discussion">
                <%= xg_html('REOPEN_DISCUSSION') %>
        </a>
    </li>
    <?php
    $adminOptionListItems[] = ob_get_contents();
    ob_end_clean();
}
if (Forum_SecurityHelper::currentUserCanTag($this->topic)) {
    $addOrEdit = mb_strlen($this->currentUserTagString) ? 'edit' : 'add';
    ob_start(); ?>
    <li dojoType="TagLink"
        _actionUrl="<%= xnhtmlentities($this->_buildUrl('topic', 'tag', array('id' => $this->topic->id, 'xn_out' => json))); %>"
        _tags="<%= xnhtmlentities($this->currentUserTagString); %>"">
        <a class="desc <%= $addOrEdit %>" href="#"><%= $addOrEdit == 'edit' ? xg_text('EDIT_YOUR_TAGS') : xg_text('ADD_TAGS') %></a>
    </li>
    <?php
    $adminOptionListItems[] = ob_get_contents();
    ob_end_clean();
}
if (Forum_SecurityHelper::currentUserCanDeleteTopic($this->topic)) {
    W_Cache::getWidget('groups')->includeFileOnce('/lib/helpers/Groups_SecurityHelper.php'); // do we need it in this context? [ywh 2008-05-28]
    XG_App::ningLoaderRequire('xg.index.bulk');
    ob_start(); ?>
    <li><a class="desc delete" href="#"
            dojoType="BulkActionLink"
            title="<%= xg_html('DELETE_THIS_DISCUSSION_Q') %>"
            _confirmMessage="<%= xg_html('ARE_YOU_SURE_DELETE_THIS_DISCUSSION') %>"
            _url="<%= xnhtmlentities($this->_buildUrl('bulk', 'remove', array('limit' => 20, 'id' => $this->topic->id, 'xn_out' => 'json'))) %>"
            _successUrl="<%= $this->_buildUrl('index', 'index') %>"
            _verb="<%= xg_html('DELETE') %>"
            _progressTitle="<%= xg_html('DELETING') %>"
            _joinPromptText="<%= xnhtmlentities(XG_JoinPromptHelper::promptToJoinOnDelete()) %>"
            ><%= xg_html('DELETE_DISCUSSION') %></a></li>
    <?php
    $adminOptionListItems[] = ob_get_contents();
    ob_end_clean();
}
if ($adminOptionListItems) { ?>
    <div class="adminbox xg_module xg_span-4 adminbox-right">
        <div class="xg_module_head">
            <h2><%= xg_html('ADMIN_OPTIONS') %></h2>
        </div>
        <div class="xg_module_body">
            <ul class="nobullets last-child">
                <?php foreach ($adminOptionListItems as $li) { echo $li; } ?>
            </ul>
        </div>
    </div>
<?php
} ?>
