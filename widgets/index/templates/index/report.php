<?php xg_header('main',xg_text('REPORT_AN_ISSUE'));
XG_App::ningLoaderRequire('xg.index.index.report');
?>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_4col first-child">
                <div class="xg_colgroup">

                    <div class="xg_3col first-child">
			<%= xg_headline(xg_text('REPORT_AN_ISSUE'))%>
                        <div class="xg_module">
                            <div class="xg_module_body">
                                <form name="xg_report_form" id="xg_report_form" action="#" method="post">
                                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                    <p><%= $this->formDescription %></p>
                                    <?php
                                        if ($this->showNotification) {
                                            echo "<dl class='" . $this->notificationClass . " msg' id='xg_report_form_notify'>\n";
                                            if ($this->notificationTitle) {
                                                echo "<dt>" . xnhtmlentities($this->notificationTitle) . "</dt>\n";
                                            }
                                            echo "<dd><p>" . xnhtmlentities($this->notificationMessage) . "</p></dd>\n";
                                            echo "</dl>\n";
                                        }
                                        else {
                                            echo "<dl class='errordesc msg' id='xg_report_form_notify' style='display: none'></dl>\n";
                                        }
                                    ?>
                                    <fieldset>
                                        <dl>
                                            <dt><span><%= xg_html('TYPE_OF_ISSUE') %></span></dt>
                                            <dd>
                                                <ul class="options">
                                                <?php
                                                    $categories = $this->formCategories;
                                                    foreach ($categories as $label => $value) {
                                                        echo $this->form->radio('category', $value, 'class="checkbox"');
                                                        echo "$label \n";
                                                    }
                                                ?>
                                                </ul>
                                            </dd>
                                        </dl>
                                        <dl>

                                            <dt><label for="xg_report_issue"><%= xg_html('DESCRIBE_YOUR_ISSUE') %></label></dt>
                                            <dd><textarea name="issue" id="xg_report_issue" rows="10" class="wide"></textarea></dd>
                                        </dl>
                                        <?php foreach ($this->formHiddenElemNames as $hidden) { ?>
                                            <%= $this->form->hidden($hidden) %>
                                        <?php } ?>
                                        <p class="buttongroup">
                                            <input type="submit" class="button" value="<%= xg_html('SEND_REPORT') %>" />
                                        </p>
                                    </fieldset>
                                </form>

                            </div>
                        </div>
                    </div>
                    <div class="xg_1col">
                        <?php $this->_widget->dispatch('embed', 'sidebar') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php

xg_footer();

?>
