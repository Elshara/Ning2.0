<?php
$spamForms = array('invite_by_emails');
?>
<form id="invite_by_emails" class="xg_module_body pad" method="post" action="<%= xnhtmlentities(xg_url($this->createUrl, array('formType' => 'enter-email-addresses'))) %>">
    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
    <input type="hidden" name="form" value="enterEmailAddresses" />
    <fieldset class="nolegend">
        <h3 class="toggle">
            <a href="#"><span><%= $this->formToOpen == 'enterEmailAddresses' ? '&#9660;' : '&#9658;' %></span> <%= xg_html('ENTER_EMAIL_ADDRESSES') %></a>
        </h3>
        <div class="indent" id="email" <%= $this->formToOpen == 'enterEmailAddresses' ? '' : 'style="display: none"' %>>
            <div class="errordesc" style="margin-bottom: 1em !important;<%= $this->enterEmailAddressesErrors ? '' : 'display: none' %>">
                <p class="last-child"><%= reset($this->enterEmailAddressesErrors) %></p>
            </div>
            <p>
                <label><%= xg_html('SEND_TO') %></label><br />
                <%= $this->form->textarea('emailAddresses','cols="60" rows="2"') %><br />
                <small><%= xg_html('SEPARATE_MULTIPLE_ADDRESSES') %></small>
            </p>
            <p>
                <%= xg_html('YOUR_MESSAGE_OPTIONAL') %><br />
                <%= $this->form->textarea('message','cols="60" rows="6"') %>
            </p>
            <p class="buttongroup"><input name="inviteEmailsSend" type="submit" class="button submit" value="<%= xnhtmlentities($this->enterEmailAddressesButtonText) %>" /></p>
        </div>
    </fieldset>
</form>
<?php
if ($this->showInviteFriendsForm) { $spamForms[] = 'invite_friends_form'; ?>
    <form id="invite_friends_form" class="xg_module_body pad" method="post" action="<%= xnhtmlentities(xg_url($this->createUrl, array('formType' => 'invite-friends'))) %>">
        <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
        <input type="hidden" name="form" value="inviteFriends" />
        <fieldset class="nolegend">
            <h3 class="toggle">
                <a href="#"><span><%= $this->formToOpen == 'inviteFriends' ? '&#9660;' : '&#9658;' %></span> <%= xnhtmlentities($this->inviteFriendsTitle) %></a>
            </h3>
            <p class="indent"><%= xnhtmlentities($this->inviteFriendsDescription) %></p>
            <div class="indent" id="friends" <%= $this->formToOpen == 'inviteFriends' ? '' : 'style="display: none;"' %>>
                <div class="errordesc" style="margin-bottom: 1em !important;<%= $this->inviteFriendsErrors ? '' : 'display: none' %>">
                    <p class="last-child"><%= reset($this->inviteFriendsErrors) %></p>
                </div>
                <?php $this->_widget->dispatch('message', 'friendList', array(array(
                        'friendDataUrl' => $this->friendDataUrl,
                        'initialFriendSet' => $this->initialFriendSet,
                        'numFriends' => $this->numFriends,
                        'numSelectableFriends' => $this->numSelectableFriends,
                        'numSelectableFriendsOnNetwork' => $this->numSelectableFriendsOnNetwork,
                        'showSelectAllFriendsLink' => $this->showSelectAllFriendsLink,
                        'showSelectFriendsOnNetworkLink' => $this->showSelectFriendsOnNetworkLink))); ?>
                <p class="clear" style="margin-top:2em">
                    <%= xg_html('YOUR_MESSAGE_OPTIONAL') %><br />
                    <%= $this->form->textarea('inviteFriendsMessage','cols="60" rows="6"') %>
                </p>
                <p class="buttongroup"><input name="inviteFriendsSend" type="submit" class="button submit" value="<%= xnhtmlentities($this->enterEmailAddressesButtonText) %>" /></p>
            </div>
        </fieldset>
    </form>
<?php
} ?>
<div dojoType="SpamWarning"
    _messageParts="<%=xnhtmlentities(json_encode($this->messageParts))%>"
    _attachTo="<%=xnhtmlentities(json_encode($spamForms))%>"
    _url="<%=$this->_buildUrl('invitation','checkMessageForSpam')%>"
    style="display:none"></div>

<form dojoType="WebAddressBookForm" class="xg_module_body pad" method="post" action="<%= xnhtmlentities(xg_url($this->createUrl, array('formType' => 'import-from-web-address-book'))) %>" <%= $this->showWebAddressBookForm ? '' : 'style="display:none"' %>>
    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
    <input type="hidden" name="form" value="webAddressBook" />
    <fieldset class="nolegend">
        <h3 class="toggle">
            <a href="#"><span><%= $this->formToOpen == 'webAddressBook' ? '&#9660;' : '&#9658;' %></span> <%= xg_html('IMPORT_FROM_WEB_ADDRESS_BOOK') %></a>
        </h3>
        <p class="indent"><span class="import-brands right"></span><%= xg_html('YAHOO_MAIL_HOTMAIL') %></p>
        <div class="indent" id="addressbook" <%= $this->formToOpen == 'webAddressBook' ? '' : 'style="display: none"' %>>
            <div id="web_address_book_error" class="errordesc" style="margin-bottom: 1em !important;<%= $this->webAddressBookErrors ? '' : 'display: none' %>">
                <p class="last-child"><%= reset($this->webAddressBookErrors) %></p>
            </div>
            <p>
                <label><%= xg_html('YOUR_EMAIL_ADDRESS') %></label><br />
                <%= $this->form->text('emailLocalPart','class="textfield" value="" size="25"') %> &#64;
                <%= $this->form->select('emailDomain', $this->emailDomains, false); %>
            </p>
            <p>
                <label><%= xg_html('PASSWORD') %></label><br />
                <%= $this->form->password('password','class="textfield" value="" size="25"') %>
            </p>
            <p class="buttongroup"><input type="submit" class="button submit" value="<%= xg_html('IMPORT_ADDRESS_BOOK') %>" /></p>
            <p><small><%= xg_html('DONT_WORRY_WE_WONT_SAVE') %></small></p>
        </div>
    </fieldset>
</form>
<form class="xg_module_body pad" method="post" action="<%= xnhtmlentities(xg_url($this->createUrl, array('formType' => 'import-from-address-book-application'))) %>" enctype="multipart/form-data" <%= $this->showEmailApplicationForm ? '' : 'style="display:none"' %>>
    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
    <input type="hidden" name="form" value="emailApplication" />
    <fieldset class="nolegend">
        <h3 class="toggle">
            <a href="#"><span><%= $this->formToOpen == 'emailApplication' ? '&#9660;' : '&#9658;' %></span> <%= xg_html('IMPORT_FROM_ADDRESS_BOOK_APPLICATION') %></a>
        </h3>
        <p class="indent"><%= xg_html('MICROSOFT_OUTLOOK_APPLE_ADDRESS_BOOK') %></p>
        <div class="indent" id="file" <%= $this->formToOpen == 'emailApplication' ? '' : 'style="display: none"' %>>
            <p><%= xg_html('UPLOAD_CSV_OR_VCF') %></p>
            <div class="errordesc" style="margin-bottom: 1em !important;<%= $this->emailApplicationErrors ? '' : 'display: none' %>">
                <p class="last-child"><%= reset($this->emailApplicationErrors) %></p>
            </div>
            <p>
                <label><%= xg_html('BROWSE_FOR_FILE') %></label><br />
                <%= $this->form->file("file",'class="file"') %>
            </p>
            <p class="buttongroup"><input type="submit" class="button submit" value="<%= xg_html('UPLOAD') %>" /></p>
            <p><small><%= xg_html('DONT_WORRY_YOULL_GET_CHANCE') %></small></p>
        </div>
    </fieldset>
</form>
<?php XG_App::ningLoaderRequire('xg.shared.SpamWarning'); ?>
<?php XG_App::ningLoaderRequire('xg.index.invitation.chooseInvitationMethod'); ?>
<?php XG_App::ningLoaderRequire('xg.index.invitation.WebAddressBookForm'); ?>
