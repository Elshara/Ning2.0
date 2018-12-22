<?php XG_App::ningLoaderRequire('xg.shared.InPlaceEditor'); ?>
<p><%= xg_html('ADD_A_REVIEW') %></p>
<dl class="msg errordesc" id="errorMsg" style="display:none">
    <?php if ($this->errors) { ?>
    	<dt><%= xg_html('ERROR') %></dt>
    	<dd>
    	    <?php foreach ($this->errors as $error) { ?>
    		    <p><%= xnhtmlentities($error) %></p>
    		<?php } ?>
    	</dd>
    <?php } ?>
</dl> 
<?php XG_App::ningLoaderRequire('xg.opensocial.application.OpenSocialReviews'); ?>
<form method="post" id="comment_form">
    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
    <input type="hidden" id="xg_opensocial_review_rating" name="rating" value="0" />
	<fieldset class="nolegend">
	    <%= $this->form->hidden('appUrl') %>
		<dl class="vcard comment last-child">
			<dt><%= xg_avatar(XN_Profile::current(), 48, 'thumb photo') %></dt>
			<dd class="easyclear margin-bottom"><%= xg_rating_widget('0', null, null, 'xg_opensocial_review_rating') %></dd>
			<dd class="easyclear">
			    <div class="texteditor"><%= $this->form->textarea('body', 'rows="4" dojoType="SimpleToolbar"'); %></div>
			    <p class="buttongroup">
				    <input type="submit" class="button" value="<%= qh(xg_html('ADD_REVIEW')) %>" id="addReviewSubmitButton" />
				</p>
			</dd>
		</dl>
	</fieldset>
</form>
