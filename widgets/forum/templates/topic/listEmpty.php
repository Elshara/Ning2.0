<?php xg_header(W_Cache::current('W_Widget')->dir, xg_text('DISCUSSION_FORUM')); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 xg_last">
        <%= $this->renderPartial('fragment_navigation', '_shared') %>
		<%= xg_headline($this->titleHtml)%>
        <%= XG_GroupHelper::groupLink() %>
        <div class="xg_module xg_column xg_span-12">
            <div class="xg_module_body">
                <p><%= xg_html('THERE_ARE_NO_DISCUSSIONS_YET') %></p>
                <?php if($this->userCanSeeAddTopicLinks) { ?>
                    <p><a <%= XG_JoinPromptHelper::promptToJoin(Topic::newTopicUrl()) %> class="bigdesc add"><%= xg_html('START_A_DISCUSSION') %></a></p>
                <?php } ?>
            </div>
            <?php
            if (! XG_App::appIsPrivate() && ! XG_GroupHelper::groupIsPrivate()) {
                xg_autodiscovery_link($this->feedUrl, $this->titleText, 'atom');
            } ?>
        </div>
    </div>
    <div class="xg_1col last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
