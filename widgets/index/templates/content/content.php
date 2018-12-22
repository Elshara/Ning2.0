<?php xg_header('manage',xg_text('ADD_CONTENT')); ?>
<?php XG_App::ningLoaderRequire('xg.index.content.content'); ?>
<script type="text/javascript">
    xg.addOnRequire(function() {
        xg_handleLaunchBarSubmit = xg.index.content.content.handleLaunchBarSubmit;
        xg_handleJoinFlowSubmit = xg.index.content.content.handleJoinFlowSubmit;
    });
</script>
    <div id="xg_body">
        <div id="add_content_module">
        <div class="xg_colgroup">
            <div class="xg_4col first-child">
                <h1><%= xg_html('ADD_CONTENT') %></h1>
            </div>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                            <dl id="content_form_notify"></dl>
                            <form id="content_form" method="post" action="<%= xnhtmlentities($this->_buildUrl('content','content')) %>" enctype="multipart/form-data">
                                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                <input type="hidden" name="successTarget"/>
                                    <%= implode("\n", $this->fragments) %>
                                <%= $this->inJoinFlow ? $this->form->hidden('joinTarget') : '' %>
                            </form>
                        </div>
                    </div>
                    <?php if ($this->displayPrelaunchButtons) {
                        $this->renderPartial('_backnext', 'embed');
                    }
                    elseif ($this->inJoinFlow) {
                        $this->renderPartial('_joinBackNext', 'embed');
                    }
                    else {
                        echo "<p><button onClick='xg.index.content.content.submitForm()'>" . xg_html('SAVE_SETTINGS') . "</button></p>";
                    }
                    ?>
                </div>
                <div class="xg_1col last-child">
                    <?php if (! $this->inJoinFlow) { xg_sidebar($this); } ?>
                </div>
            </div>
        </div>
        </div>
        <div id="adding_content_module" style="display: none">
          <div class="xg_colgroup">
            <div class="xg_4col first-child">
                <h1><%= xg_html('UPLOADING_CONTENT') %></h1>
            </div>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                            <h3><img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/spinner.gif') %>" alt="<%= xg_html('UPLOADING') %>" class="left" style="margin:0 15px 60px 0" /><strong><%= xg_html('LEAVE_WINDOW_OPEN') %></strong></h3>
                            <p><%= xg_html('WHEN_UPLOADING_IS_COMPLETE') %></p>
                        </div>
                    </div>
                </div>
                <div class="xg_1col">
                    <div class="xg_1col first-child">
                    </div>
                </div>
            </div>
          </div>
        </div>
    </div>
<?php xg_footer(); ?>
