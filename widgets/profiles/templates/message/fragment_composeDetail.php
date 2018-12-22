                <?php if ($this->action === 'new') {
                    if (! $this->allowRecipientChange) {
                        $recipient = $this->profiles[$this->recipients[0]]; ?>
                        <div class="title_detail">
                            <%= xg_avatar($recipient, 64) %>
                            <h2><%= xg_html('SEND_A_MESSAGE') %></h2>
                            <p>
                                <%= xg_html('TO_RECIPIENT', 'class="xg_lightfont"', xg_userlink($recipient)) %><br />
                                <%= xg_html('FROM_SENDER', 'class="xg_lightfont"', xg_text('YOU')) %>
                            </p>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <%= $this->renderPartial('fragment_titleDetail', 'message', array('message' => $this->message, 'profiles' => $this->profiles, 'sender' => $this->msgSender, 'recipientList' => $this->msgRecipientList)) %>
                <?php } ?>
