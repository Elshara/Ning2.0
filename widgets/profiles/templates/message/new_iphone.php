<?php XG_IPhoneHelper::header('members', $this->pageTitle, $this->profile, array('contentClass' => 'compose','displayHeader' => false, 'hideNavigation' => true)); ?>
	<form id="compose" class="panel" method="post" action="<%= xnhtmlentities($this->_buildUrl('message', 'createForScreenName', array('screenName' => $this->user->title))) %>">
		<div id="header">
		<strong><%= xg_html('SEND_MESSAGE') %></strong>
		<a class="title-button" id="add" onclick="javascript:void(0);"><%= xg_html('SEND') %></a>
		<a class="title-button" id="cancel" onclick="javascript:void(0);"><%= xg_html('CANCEL') %></a>
		</div><!--/#header-->
	    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
	    <fieldset>
	    	<div class="row">
	    		<label for="post"><%= xg_html('MESSAGE_COLON') %></label>
	    		<textarea name="message" class="lighter" _default="<%= qh(xg_html('TAP_HERE_TO_BEGIN_WRITING')) %>"></textarea>
	    	</div>
	    </fieldset>
	</form>
    <script>initComposeForm()</script>
    <h3><%= xg_html('SEND_MESSAGE_TO_COLON') %></h3>
    <div class="about about-64">
        <div class="ib">
        	<%= xg_avatar($this->profile, 64, null, '', true) %>
        </div>
		<div class="tb">
		       	<span class="name"><%= xnhtmlentities(xg_username($this->user->title)) %></span>
			<?php
	        if ($this->userAgeSex) { ?>
	   			<span class="ageSex"><%= xnhtmlentities($this->userAgeSex) %></span>
	   		<?php
	        }
	        if ($this->userLocation) { ?>
	        	<span class="location"><%= xnhtmlentities($this->userLocation) %></span>
			<?php
	        } ?>
	    </div>
     </div>
<?php xg_footer(NULL,array('contentClass' => 'compose', 'regularPage' => $this->_buildUrl('message','newFromProfile', array('screenName' => $this->user->title)))); ?>
