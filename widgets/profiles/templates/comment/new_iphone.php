<?php XG_IPhoneHelper::header('members', xg_text('ADD_COMMENT_TO_XS_WALL', xnhtmlentities(xg_username($this->user->title))), $this->profile, array('contentClass' => 'compose','displayHeader' => false, 'hideNavigation' => true)); ?>
	<form id="compose" class="panel" method="post" action="<%= xnhtmlentities($this->_buildUrl('comment', 'create', array('screenName' => $this->user->title))) %>">
		<div id="header">
		<strong><%= xg_html('ADD_COMMENT') %></strong>
		<a class="title-button" id="add" onclick="javascript:void(0);"><%= xg_html('ADD') %></a>
		<a class="title-button" id="cancel" onclick="javascript:void(0);"><%= xg_html('CANCEL') %></a>
		</div><!--/#header-->
	    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
	    <input type="hidden" name="attachedTo" value="<%= $this->user->title %>" />
	    <input type="hidden" name="attachedToType" value="User" />
	    <input type="hidden" name="successTarget" value="<%= xnhtmlentities($this->_buildUrl('profile', 'show', array('screenName' => $this->user->title))) %>" />
		<?php XG_IPhoneHelper::outputErrors($this->errors, true); ?>
	    <fieldset>
	    	<div class="row">
	    		<label for="post"><%= xg_html('COMMENT') %></label>
				<textarea name="comment" class="lighter" _required="<%=qh(xg_html('PLEASE_ENTER_SOMETHING_FOR_YOUR'))%>" _default="<%= qh(xg_html('TAP_HERE_TO_BEGIN_WRITING')) %>"></textarea>
	    	</div>
	    </fieldset>
	</form>
    <script>initComposeForm()</script>
    <h3><%= xg_html('ADD_TO_COMMENT_WALL') %></h3>
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
<?php xg_footer(NULL,array('contentClass' => 'compose','displayFooter' => false)); ?>
