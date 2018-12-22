<?php XG_IPhoneHelper::header('invitation', $this->pageTitle, $this->profile, array('contentClass' => 'compose','displayHeader' => false, 'hideNavigation' => true)); ?>
<form id="compose" class="panel" method="post" action="<%= xnhtmlentities($this->_buildUrl('invitation', 'create', array('previousUrl' => $this->previousUrl))) %>">
	<div id="header">
	<strong><%= xg_html('INVITE') %></strong>
	<a class="title-button" id="add" onclick="javascript:void(0);"><%= xg_html('SEND') %></a>
	<a class="title-button" id="cancel" onclick="javascript:void(0);"><%= xg_html('CANCEL') %></a>
	</div><!--/#header-->
    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
    <?php XG_IPhoneHelper::outputErrors($this->errors, true); ?>
    <fieldset>
    	<div class="row">
    		<label for="post"><%= xg_html('EMAIL_ADDRESSES_COLON') %></label>
			<textarea name="emailAddresses" class="lighter" _required="<%=qh(xg_html('PLEASE_ENTER_EMAIL_ADDRESSES'))%>" _default="<%= qh(xg_html('TAP_HERE_TO_BEGIN_WRITING')) %>"></textarea>
	   	</div>
    	<div class="row">
    		<label for="post"><%= xg_html('CUSTOM_MESSAGE_COLON') %></label>
			<textarea name="message" class="lighter" _default="<%= qh(xg_html('JOIN_THIS_EXCITING_NETWORK')) %>"></textarea>
	   	</div>
    </fieldset>
</form>
<script>initComposeForm()</script>
<?php xg_footer(NULL,array('contentClass' => 'compose','displayFooter' => false)); ?>
