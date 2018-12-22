<?php
/**
 * Displays the fields for editing a category.
 *
 * @param $category XN_Content|W_Content  the category
 * @param $topicCount integer  the number of topics in this category (null implies 0)
 */
$topicCount = $topicCount ? $topicCount : 0;
$membersCanAddTopics = $category->my->membersCanAddTopics == 'Y';
$membersCanReply = $category->my->membersCanReply == 'Y';
if ($membersCanAddTopics) { $membersCanReply = true; } ?>
<fieldset class="fieldset category move" dojotype="CategoryEditor" _id="<%= $category->id %>" _alternativeids="<%= $category->my->alternativeIds %>" _topiccount="<%= $topicCount %>">
	<dl>
		<?php
			$n = uniqid();
			// edit.js replaces $n for new category editors. Keep the name "members_can_add_topics_*" in sync with edit.js [Jon Aquino 2007-04-16]
			// Keep the checked/disabled logic in sync with CategoryEditor.js [Jon Aquino 2007-03-26]
		?>
		<dt><label for="category_title_<%= $n %>"><%= xg_html('CATEGORY_TITLE') %></label></dt>
		<dd><input id="category_title_<%= $n %>" type="text" class="textfield" size="50" maxlength="<%= Category::MAX_TITLE_LENGTH %>" value="<%= xnhtmlentities($category->title) %>" /></dd>
		<dd>
			<label><input name="members_can_add_topics_<%= $n %>" value="Y" type="radio" class="radio" <%= $membersCanAddTopics ? 'checked="checked"' : '' %> /><%= xg_html('ALLOW_MEMBERS_TO_START_DISCUSSIONS') %></label><br />
			<label><input name="members_can_add_topics_<%= $n %>" value="N" type="radio" class="radio" <%= ! $membersCanAddTopics ? 'checked="checked"' : '' %> /><%= xg_html('ONLY_I_CAN_START_DISCUSSIONS') %></label><br />
			<span <%= $membersCanAddTopics ? 'class="disabled"' : '' %> style="margin-left:2em">
				<label><input type="checkbox" class="checkbox" <%= $membersCanReply ? 'checked="checked"' : '' %> <%= $membersCanAddTopics ? 'disabled="disabled"' : '' %> /><%= xg_html('ALLOW_MEMBERS_TO_REPLY') %></label>
			</span>
		</dd>
		<dt><label for="category_description_<%= $n %>"><%= xg_html('DESCRIPTION') %></label></dt>
		<dd><textarea id="category_description_<%= $n %>" rows="2" cols="50"><%= xnhtmlentities($category->description) %></textarea></dd>
	</dl>
	<ul class="actions">
		<li style="display:none;"><a href="#" class="delete desc"><%= xg_html('DELETE') %></a></li>
		<li><a href="#" class="add desc"><%= xg_html('ADD_ANOTHER_CATEGORY') %></a></li>
	</ul>
</fieldset>
