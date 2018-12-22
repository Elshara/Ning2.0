<?php
/** Partial template for rendering the 'Add a chatter' form
 * @param $profile XN_Profile Profile of the user whose chatter we're adding to
 * @param $showCommentUrl string optional URL that the user should be redirected to
 *   after a successful comment is submitted; used when the current comment page is
 *   not the page on which new comments should appear
 * @param $isOwner boolean; used to toggle the form and set the default state to closed if being viewed by the owner.
 */

$showCommentUrl = isset($showCommentUrl) ? $showCommentUrl : '';
$currentUrl = XG_HttpHelper::currentUrl();
?>
<a name="add_comment"></a>
<?php if (User::isMember($this->_user)) { ?>
    <?php if ($isOwner) { ?>
        <p id="add-comment" class="toggle last-child"><span state="closed"><!--[if IE]>&#9658;<![endif]--><![if !IE]>&#9654;<![endif]></span> <%= xg_html('ADD_A_COMMENT') %></p>
    <?php } else if ($profile->isLoggedIn()) { ?>
        <p id="add-comment"><%= xg_html('ADD_A_COMMENT') %></p>
    <?php } else { ?>
        <p id="add-comment"><%= xg_html('LEAVE_A_COMMENT_FOR_USERNAME', xnhtmlentities(xg_username($profile))) %></p>
<?php } ?>

  <dl id="xg_profiles_chatterwall_post_notify" style="display: none"></dl>
  <form id="xg_profiles_chatterwall_post" action="<%= xnhtmlentities($this->_buildUrl('comment','create')) %>" method="post" <%= $isOwner ? 'style="display:none;"' : '' %>>
    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
    <fieldset class="nolegend">
      <dl class="vcard comment xg_lightborder">
        <dt><img class="photo" src="<%= xnhtmlentities(XG_UserHelper::getThumbnailUrl($this->_user,48,48)) %>" height="48" width="48" alt=""/></dt>
        <dd>
            <div class="texteditor">
                <textarea id="xg_profiles_chatterwall_post_comment" dojoType="SimpleToolbar" name="comment" rows="5" style="width:99%"></textarea>
            </div>
            <p class="buttongroup">
                <input type="submit" class="button" value="<%= xg_html('ADD_COMMENT') %>" />
            </p>
        </dd>
      </dl>
    </fieldset>
    <input type="hidden" name="successTarget" value="<%= xnhtmlentities($currentUrl) %>" />
    <input type="hidden" id="xg_profiles_chatterwall_ownerName" name="attachedTo" value="<%= xnhtmlentities(xg_username($profile->screenName)) %>" />
    <input type="hidden" id="xg_profiles_chatterwall_attachedTo" name="attachedTo" value="<%= xnhtmlentities($profile->screenName) %>" />
    <input type="hidden" id="xg_profiles_chatterwall_attachedToType" name="attachedToType" value="User" />
    <input type="hidden" name="showCommentUrl" value="<%= xnhtmlentities($showCommentUrl) %>" />
  </form>
<?php } else { ?>
  <h3><%= xg_html('YOU_NEED_TO_BE_MEMBER_COMMENTS', xnhtmlentities(XN_Application::load()->name)) %></h3>
  <?php if ($this->_user->isLoggedIn() && User::isPending($this->_user)) { ?>
      <p><%= xg_html('YOUR_MEMBERSHIP_TO_X_IS_PENDING_APPROVAL', xnhtmlentities(XN_Application::load()->name)) %></p>
  <?php } else { ?>
      <p><%= xg_html('SIGN_UP_OR_SIGN_IN', 'href="' . xnhtmlentities(XG_HttpHelper::signUpUrl()) . '"', 'href="' . xnhtmlentities(XG_HttpHelper::signInUrl()) . '"') %></p>
  <?php } ?>
<?php } ?>
