<?php xg_header(null, xg_text('BANNED'), null, array('displayHeader' => false, 'xgDivClass' => 'account', 'hideNingbar' => true)); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_2col first-child">
            <div class="xg_module">
                <div class="xg_module_body pad">
                    <?php
                    if ($this->showNotification) {
                        echo "<dl class='" . $this->notificationClass . " msg' id='xg_banned_message_form_notify'>\n";
                        if ($this->notificationTitle) {
                            echo "<dt>" . xnhtmlentities($this->notificationTitle) . "</dt>\n";
                        }
                        echo "<dd><p>" . xnhtmlentities($this->notificationMessage) . "</p></dd>\n";
                        echo "</dl>\n";
                    } ?>
                    <h3><%= xg_html('YOU_HAVE_BEEN_BANNED_FROM_APPNAME', xnhtmlentities(XN_Application::load()->name)) %></h3>
                    <?php
                    if (! $this->showMessageArea) { ?>
                        <p class="last-child"><%= xg_html('SORRY_USERNAME_YOU_CANNOT_ACCESS', xnhtmlentities(XG_UserHelper::getFullName(XN_Profile::current())), xnhtmlentities(XN_Application::load()->name)) %></p>
                    <?php
                    } else { ?>
                        <p><%= xg_html('SORRY_USERNAME_YOU_CANNOT_ACCESS_IF_YOU_THINK', xnhtmlentities(XG_UserHelper::getFullName(XN_Profile::current())), xnhtmlentities(XN_Application::load()->name)) %></p>
                        <form method="POST" action="#" id="xg_banned_message_form">
                            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                            <p class="last-child">
                                <label for="banned-message"><%= xg_html('MESSAGE_TO_ADMINISTRATOR') %></label><br />
                                <textarea id="banned-message" name="message" rows="5" cols="46" style="width:420px"></textarea><br />
                                <input type="submit" class="button" value="<%= xg_html('SEND_MESSAGE') %>" />
                            </p>
                        </form>
                    <?php
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php xg_footer(null, array('displayFooter' => false)) ?>
