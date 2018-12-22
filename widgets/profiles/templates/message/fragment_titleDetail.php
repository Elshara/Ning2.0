            <div class="title_detail">
                <%= xg_avatar($profiles[$message->sender], 64) %>
                <h2><%= xnhtmlentities(Profiles_MessageHelper::formatSubjectForDisplay(xg_excerpt($message->subject, Profiles_MessageHelper::MAX_SUBJECT_LENGTH))) %></h2>
                <p>
                    <?php $numRecipients = count($recipientList);
                    $limit = Profiles_MessageHelper::MAX_RECIPIENTS_TO_DISPLAY;
                    if ($numRecipients > $limit + Profiles_MessageHelper::MIN_N_MORE - 1) {
                        XG_App::ningLoaderRequire('xg.profiles.message.show'); ?>
                        <span id="xj_recipients_more"><%= xg_html('FROM_SENDER_TO_RECIPIENT_STYLED', $numRecipients, 'class="xg_lightfont"', $sender, 'class="xg_lightfont"', 'class="xg_lightfont"', implode(xg_html('RECIPIENT_SEPARATOR'), array_slice($recipientList, 0, $limit)), xg_html('N_MORE_HELLIP_LINK', $numRecipients - $limit, ' href="#" id="xj_expand_recipients"')) %></span>
                        <span id="xj_recipients_rest" style="display: none;"><%= xg_html('FROM_SENDER_TO_RECIPIENT_STYLED', $numRecipients, 'class="xg_lightfont"', $sender, 'class="xg_lightfont"', 'class="xg_lightfont"', implode(xg_html('RECIPIENT_SEPARATOR'), array_slice($recipientList, 0, $numRecipients - 1)), $recipientList[$numRecipients - 1]) %></span><br />
                    <?php } else { ?>
                        <%= xg_html('FROM_SENDER_TO_RECIPIENT_STYLED', $numRecipients, 'class="xg_lightfont"', $sender, 'class="xg_lightfont"', 'class="xg_lightfont"', implode(xg_html('RECIPIENT_SEPARATOR'), array_slice($recipientList, 0, $numRecipients - 1)), $recipientList[$numRecipients - 1]) %><br />
                    <?php } ?>
                    <span class="xg_lightfont"><%= xg_html('SENT_T', xg_elapsed_time($message->createdDate)) %></span>
                </p>
            </div>