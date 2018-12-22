<?php xg_header('manage', $title = xg_text('MANAGE_FORUM'), null, array('forceDojo'=>true));
?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
		<%= xg_headline(xg_text('MANAGE_FORUM'))%>
        <?php
            if ($_GET['saved'] == 1) {
                echo "<dl class=\"success msg\"><dt>" . xg_html('SUCCESS_EXCLAMATION') . "</dt>";
                echo "<dd><p>" . xg_html('YOUR_CHANGES_HAVE_BEEN_SAVED') . "</p></dd></dl>\n";
            }
        ?>
        <div class="xg_module">
            <form action="<%= xnhtmlentities($this->_buildUrl('manage', 'update')) %>" method="post">
                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                <div class="xg_module_body">
                    <h3><%= xg_html('DISCUSSION_STYLE') %></h3>
                    <fieldset class="nolegend">
                        <p><%= xg_html('FORUM_REPLIES_ARE_COLON') %></p>
                        <ul class="nobullets">
                            <li><label><input class="radio" type="radio" name="threadingModel" <%= $this->threadingModel == 'flat' ? 'checked="checked" ' : '' %> value="flat" /><%= xg_html('FLAT_DESCRIPTION') %></label></li>
                            <li><label><input class="radio" type="radio" name="threadingModel" <%= $this->threadingModel == 'threaded' ? 'checked="checked" ' : '' %> value="threaded" /><%= xg_html('THREADED_DESCRIPTION') %></label></li>
                        </ul>
                    </fieldset>
                    <h3><%= xg_html('MAIN_FORUM_PAGE_STYLE') %></h3>
                    <fieldset class="nolegend">
                        <p><%= xg_html('MAIN_FORUM_PAGE_SHOW_COLON') %></p>
                        <ul class="nobullets">
                        <li><label><input class="radio" type="radio" name="forumMainStyle" <%= $this->forumMainStyle == 'categories' ? 'checked="checked" ' : '' %> value="categories" id="categoryStyleOption" /><%= xg_html('CATEGORIES') %></label></li>
                        <li><label><input class="radio" type="radio" name="forumMainStyle" <%= $this->forumMainStyle == 'latestByTime' ? 'checked="checked" ' : '' %> value="latestByTime" id="byTimeStyleOption" /><%= xg_html('LATEST_DISCUSSIONS_BY_TIME') %></label></li>
                        <li><label><input class="radio" type="radio" name="forumMainStyle" <%= $this->forumMainStyle == 'latestByCategory' ? 'checked="checked" ' : '' %> value="latestByCategory" id="latestByCategory" /><%= xg_html('LATEST_DISCUSSIONS_BY_CATEGORY') %></label></li>
                        </ul>
                    </fieldset>
                </div>
                <div>
                    <div id="category_container" class="xg_module_body">
                        <h3><%= xg_html('CATEGORIES') %></h3>
                        <?php
                        for ($i = 0; $i < count($this->categories); $i++) {
                            $this->renderPartial('fragment_categoryEditor', 'manage', array('category' => $this->categories[$i], 'topicCount' => $this->topicCounts[$i]));
                        } ?>
                        <p class="buttongroup">
                            <input id="save_button" style="display:none" type="submit" class="button button-primary" value="<%= xg_html('SAVE') %>" />
                            <a class="button" href="<%= W_Cache::getWidget('main')->buildUrl('admin', 'manage') %>"><%= xg_html('CANCEL')%></a>
                        </p>
                    </div>
                </div>
                <input type="hidden" name="data" value="" />
                <?php
                $templateCategory = Category::create('', '');
                $templateCategory->my->membersCanAddTopics = 'Y';
                $templateCategory->my->membersCanReply = 'Y';
                ob_start();
                $this->renderPartial('fragment_categoryEditor', 'manage', array('category' => $templateCategory));
                $categoryEditorTemplate = ob_get_contents();
                ob_end_clean(); ?>
                <input type="hidden" id="category_editor_template" value="<%= xnhtmlentities(str_replace("\n", " ", $categoryEditorTemplate)) %>" />
            </form>
        </div>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php XG_App::ningLoaderRequire('xg.forum.manage.CategoryEditor', 'xg.forum.manage.index'); ?>
<?php xg_footer(); ?>
