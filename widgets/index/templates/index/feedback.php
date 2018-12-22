<?php xg_header('main',xg_text('SEND_FEEDBACK'));
XG_App::ningLoaderRequire('xg.index.index.feedback');
?>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_4col first-child">
                <div class="xg_colgroup">
                    <div class="xg_3col first-child">
						<%= xg_headline(xg_text('SEND_FEEDBACK'))%>
                        <div class="xg_module">
                            <div class="xg_module_body pad">
                                <form name="xg_feedback_form" id="xg_feedback_form" action="#" method="post" style="width: 495px;">
                                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                    <p>
                                        <%= xg_html('USE_THIS_FORM_TO_SEND_FEEDBACK_TO_NC', 'href="http://' .$_SERVER['HTTP_HOST'] . User::quickProfileUrl(XN_Application::load()->ownerName) . '"', xnhtmlentities($this->ownerName), 'href="http://' . $_SERVER['HTTP_HOST'] . '"', XN_Application::load()->name) %>
                                    </p>
                                    <?php
                                        if ($this->showNotification) {
                                            echo "<dl class='" . $this->notificationClass . " msg' id='xg_feedback_form_notify'>\n";
                                            if ($this->notificationTitle) {
                                                echo "<dt>" . xnhtmlentities($this->notificationTitle) . "</dt>\n";
                                            }
                                            echo "<dd><p>" . xnhtmlentities($this->notificationMessage) . "</p></dd>\n";
                                            echo "</dl>\n";
                                        }
                                        else {
                                            echo "<dl class='errordesc msg' id='xg_feedback_form_notify' style='display: none'></dl>\n";
                                        }
                                    ?>
                                    <fieldset style="width: 495px">
                                        <div class="legend"><%= xg_html('GIVE_US_YOUR_FEEDBACK') %></div>
                                        <p>
                                        <textarea name="feedback" cols="67" id="xg_feedback_feedback" rows="10"></textarea>
                                        </p>
                                        <p class="buttongroup">
                                            <input type="submit" class="button" value="<%= xg_html('SEND_FEEDBACK') %>" />
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
