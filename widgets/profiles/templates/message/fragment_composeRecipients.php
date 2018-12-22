                            <?php

                            $main = W_Cache::getWidget('main');
                            $main->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
                            $isReply = $this->action === Profiles_MessageHelper::COMPOSE_REPLY;
                            
                            if (! $this->allowRecipientChange) { ?>
                                <input type="hidden" id="fixedRecipients" name="fixedRecipients" value="<%= xnhtmlentities($this->recipients[0]) %>" />
                            <?php } else if ($this->allFriends) { ?>
                                <input type="hidden" name="friendSet" value="<%= Index_MessageHelper::FRIENDS_ON_NETWORK %>" />
                                <input type="hidden" name="screenNamesIncluded" value="" />
                                <input type="hidden" name="screenNamesExcluded" value="" />
                            <?php } else { ?>
                                <dl class="easyclear">
                                    <dt class="align-right"><label for="recipients" id="xj_label_recipients"><%= $this->action === Profiles_MessageHelper::COMPOSE_NEW ? xg_html('SEND_TO') : xg_html(strtoupper($this->action) . '_TO') /** @non-mb */%></label></dt>
                                    <?php if (count($this->recipients) > 0) { ?>
                                        <dd>
                                            <input type="hidden" id="presetRecipients" name="presetRecipients" value="" />
                                            <?php XG_App::includeFileOnce('/lib/XG_UserHelper.php');
                                            $recipients = array();
                                            foreach ($this->recipients as $user) {
                                                if (is_object($this->profiles[$user]) && ($user === $this->profiles[$user]->screenName)) {
                                                    $id = $this->profiles[$user]->screenName;
                                                    $displayName = XG_UserHelper::getFullName($this->profiles[$user]);
                                                } else {
                                                    // an email address
                                                    $id = $displayName = $user;
                                                }
                                                $recipients[] = '<a href="#" class="recipient-delete" _recipient="' . $id . '">' . xg_html('DELETE') . '</a> ' . xnhtmlentities($displayName);
                                            } ?>
                                            <ul class="recipient-delete">
                                                <%= '<li>' . implode(',</li><li>', $recipients) . '</li>' %>
                                                <?php if ($isReply) { ?>
                                                    <li class="leftpad"><small>(<a href="#" class="xj_add_more_link"><%= xg_html('ADD_MORE') %></a>)</small></li>
                                                <?php } ?>
                                            </ul>
                                        </dd>
                                    <?php } ?>
                                    <dd<%= $isReply ? ' class="xj_hidden_compose_field" style="display:none;"' : '' ; %>>
                                        <input type="text" class="textfield" id="recipients" name="recipients" /><br />
                                        <small><%= xg_html('ENTER_EMAIL_ADDRESSES_SEPARATE') %></small><br />
                                    </dd>
                                    <?php
                                    if (XG_App::constant('Profiles_MessageHelper::FRIEND_LIST_DISPLAYED_ON_COMPOSE_PAGE')) {
                                        $numFriendsOnNetwork = Index_MessageHelper::numberOfFriendsOnNetwork($this->_user->screenName);
                                        $friendDataUrl = $main->buildUrl('message', 'friendData', array('xn_out' => 'json'));
                                        if ($numFriendsOnNetwork) { ?>
                                            <dd<%= $isReply ? ' class="xj_hidden_compose_field" style="display:none;"' : '' ; %>>
                                                <!-- friend selector goes here -->
                                                <p class="toggle xj_toggle"><a href="#"><span><%= $this->allFriends ? '&#9660;' : '&#9658;' %></span><%=xg_html('SELECT_RECIPIENTS_FROM_MY')%></a></p>
                                                <div class="indent xj_friends"<%= $this->allFriends ? '' : ' style="display: none;"' %>>
                                                    <?php $main->dispatch('message', 'friendList', array(array(
                                                        'initialFriendSet' => $this->allFriends ? Index_MessageHelper::FRIENDS_ON_NETWORK : null,
                                                        'friendDataUrl' => $friendDataUrl,
                                                        'numFriends' => $numFriendsOnNetwork,
                                                        'numSelectableFriends' => $numFriendsOnNetwork,
                                                        'numSelectableFriendsOnNetwork' => $numFriendsOnNetwork,
                                                        'showSelectAllFriendsLink' => FALSE,
                                                        'showSelectFriendsOnNetworkLink' => TRUE,
                                                    ))); ?>
                                                </div>
                                            </dd>
                                        <?php
                                        }
                                    }?>
                                </dl>
                            <?php } ?>
