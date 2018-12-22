<?php xg_header($this->tab, strip_tags($this->titleHtml), null, array('metaKeywords' => $this->metaKeywords, 'metaDescription' => $this->metaDescription)); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Profiles_HtmlHelper::blogSubMenu($this->_widget)) ?>
			<%= xg_headline($this->titleHtml, array('avatarUser' => $this->user))%>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <div class="xg_module_head notitle"></div>
                        <div class="xg_module_body">
                            <p><%= xg_html('NO_BLOG_POSTS_TAGGED_X_CHECK', xnhtmlentities($_GET['tag']), 'href="' . W_Cache::getWidget('forum')->buildUrl('topic', 'listForTag', array('tag' => $_GET['tag'])) . '"', 'href="' . W_Cache::getWidget('video')->buildUrl('video', 'listTagged', array('tag' => $_GET['tag'])) . '"', 'href="' . xnhtmlentities(W_Cache::getWidget('photo')->buildUrl('photo', 'listTagged', array('tag' => $_GET['tag']))) . '"') %></p>
                            <p class="buttongroup"><a <%= XG_JoinPromptHelper::promptToJoin($this->_buildUrl('blog', 'new')) %> class="button"><%= xg_html('ADD_BLOG_POST') %></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
