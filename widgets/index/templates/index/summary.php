<?php xg_header('manage',xg_text('SUMMARY')); ?>

<?php XG_App::ningLoaderRequire('xg.index.index.summary'); ?>
<script type="text/javascript">
    xg.addOnRequire(function() {
        xg_handleLaunchBarSubmit = xg.index.index.summary.handleLaunchBarSubmit;
    });
</script>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_4col first-child">
                <h1><%= xg_html('SUMMARY') %></h1>

            </div>

            <div class="xg_colgroup">
                <div class="xg_2col first-child">
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                            <big>
                                <p>
                                    <%= xg_html('YOUR_SITE_IS_READY') %>
                                </p>

                                <ul class="checklist">
                                    <?php foreach ($this->steps as $step) {
                                        if ($step['name'] != 'Summary') {
                                            echo '<li class="' . $step['state'] . '">';
                                            echo '<a href="' . $this->_widget->buildUrl($step['controller'], $step['action']) . '">';
                                            echo $step['displayName'] . ':</a> ' .$this->stateDisplayName[$step['state']] . "</li>\n";
                                        }
                                    }
                                    ?>
                                </ul>
                            </big>
                        </div>
                    </div>
                    <?php
                        $this->renderPartial('_backnext', 'embed');
                    ?>
                </div>
            </div>
        </div>
    </div>

<?php xg_footer(); ?>
