<?php
$allowsInviteRequests = $group->my->allowInvitationRequests == 'Y';
$this->form = new XNC_Form();
if (! XN_Profile::current()->isLoggedIn()) {
    $href = XG_JoinPromptHelper::promptToJoin(XG_HTTPHelper::addParameter(XG_HttpHelper::currentUrl(),'requestInv','true'));
    $createOwnHref = trim(XG_JoinPromptHelper::promptToJoin($this->_buildUrl('group', 'new')));
} else {
    $href = 'href="#" onclick="dojo.style.toggleShowing(dojo.byId(\'requestform\'))"';
    $createOwnHref = 'href="' . $this->_buildUrl('group', 'new') . '"';
}
?>
<div class="xg_module">
<?php if ($_GET['invitationRequestSent'] == 'yes' || $userHasRequested) { ?>
    <div class="xg_module_body success topmsg">
        <?php if (!$userHasRequested) { ?>
            <h3><%= xg_html('REQUEST_SENT') %></h3>
        <?php } ?>
        <p><%= xg_html('YOUR_GROUP_REQUEST_SENT') %></p>
    </div>
<?php } else { ?>
    <div class="xg_module_body notification topmsg">
        <h3><%= xg_html('MEMBERSHIP_IS_BY_INVITATION_ONLY') %></h3>
        <?php if ($allowsInviteRequests) { ?>
            <p><%= xg_html('GROUP_IS_ACCEPTING_MEMBERS',xnhtmlentities($group->title), 'id="requestInvitation" ' . $href) %>
            <fieldset id="requestform" <%= $_GET['requestInv'] ? '' : 'style="display: none;"' %>>
                <form id="invitation_request_form" action="<%= xnhtmlentities($this->_buildUrl('invitationrequest', 'create', array('groupId' => $group->id))) %>" method="post">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <p><label for="requestmsg"><%= xg_html('MESSAGE') %></label>
                    <%= xg_html('OPTIONAL_PARENTHASIES') %><br/>
                    <%= $this->form->textarea('message', 'id="invitation_request_message" rows="3" cols="60" _maxlength="' . GroupInvitationRequest::MAX_MESSAGE_LENGTH . '"') %></p>
                    <p class="last-child">
                        <input class="button" type="submit" value="<%= xg_html('SEND_REQUEST') %>"/>
                    </p>
                </form>
            </fieldset>
        <?php } elseif ($canCreate) { ?>
            <p class="last-child"><%= xg_html('WHY_NOT_CREATE_YOUR_OWN', $createOwnHref) %></p>
        <?php } ?>
    </div>
<?php } ?>
</div>


