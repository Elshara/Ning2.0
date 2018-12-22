<?php XG_IPhoneHelper::header('forum', xg_html('NEW_DISCUSSION'), $this->profile, array('contentClass' => 'compose','displayHeader' => false, 'hideNavigation' => true)); ?>
    <form id="compose" class="panel" id="add_topic_form" action="<%= xnhtmlentities($this->formUrl) %>" method="post" enctype="multipart/form-data">
        <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
		<div id="header">
		<strong><%= xg_html('NEW_DISCUSSION') %></strong>
		<a class="title-button" id="add" onclick="javascript:void(0);"><%= xg_html('SUBMIT') %></a>
		<a class="title-button" id="cancel" onclick="javascript:void(0);"><%= xg_html('CANCEL') %></a>
		</div><!--/#header-->
        <%= XG_IPhoneHelper::outputErrors($this->errors, true) %>
        <fieldset>
			<div class="row">
            	<label for="title"><%= xg_html('TITLE_COLON') %></label>
            	<input name="title" type="text" id="title" _required="<%=qh(xg_html('PLEASE_ENTER_TITLE'))%>" />
            </div>
            <div class="row">
            	<label for="post"><%= xg_html('POST_COLON') %></label>
            	<textarea name="description" id="post" class="lighter" _required="<%=qh(xg_html('PLEASE_ENTER_FIRST_POST'))%>" _default="<%=qh(xg_html('TAP_HERE_TO_BEGIN_WRITING'))%>"></textarea>
            </div>
            <?php
            if (count($this->categories) > 0) {
                $categoryIdsToTitles = array();
                foreach ($this->categories as $category) {
                    $categoryIdsToTitles[$category->id] = $category->title;
                } ?>
                <div class="row">
                    <label for="categories"><%= xg_html('CATEGORIES_COLON') %></label>
                    <%= $this->form->select('categoryId', $categoryIdsToTitles, false, 'id="category"') %>
                </div>
            <?php
            } ?>
            <div class="row">
            	<label for="tags"><%= xg_html('TAGS_COLON') %></label>
            	<input type="text" name="tags" value="" id="tags" />
            </div>
    </form>
    <script>initComposeForm()</script>
<?php xg_footer(NULL,array('contentClass' => 'compose', 'displayFooter' => false)); ?>