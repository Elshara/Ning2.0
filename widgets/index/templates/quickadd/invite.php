<?php
/*  $Id: $
 *
 */
$main = W_Cache::getWidget('main');

$main->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
$numFriendsAcrossNing = Index_MessageHelper::numberOfFriendsAcrossNing($this->_user->screenName);
$numFriendsOnNetwork = Index_MessageHelper::numberOfFriendsOnNetwork($this->_user->screenName);
$friendDataUrl = $this->_buildUrl('invitation', 'friendData', array('xn_out' => 'json'))
?>
<div class="xg_floating_module" style="display:none">
    <div class="xg_floating_container xg_floating_container_wide xg_module">
        <div class="xg_module_head">
            <h2><span class="png-fix"><img src="<%=xg_cdn('/xn_resources/widgets/index/gfx/icon/quickpost/invite.png')%>" alt="" /></span><%=xg_html('INVITE_FRIENDS')%></h2>
        </div>
        <div class="xg_module_body">
            <form id="xg_quickadd_invite" action="<%=$main->buildUrl('invitation','createQuick','/.txt?xn_out=json')%>" method="post">
                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                <div class="msg success" id="xg_quickadd_invite_notify_success" style="display:none"></div>
                <div class="msg" id="xg_quickadd_invite_notify" style="display:none"></div>
                <fieldset class="nolegend last-child">
                    <p>
                        <label><%=xg_html('SEND_TO_COLON')%><input type="text" name="emailAddresses" class="textfield wide" /></label><br />
                        <small><%=xg_html('ENTER_EMAIL_ADDRESSES_SEPARATE')%></small><br />
                    </p>
                    <?php
                    if ($numFriendsAcrossNing - $numFriendsOnNetwork > 0) {?>
                        <p class="toggle xj_toggle"><a href="#"><span>&#9658;</span><%=xg_html('SELECT_RECIPIENTS_FROM_MY')%></a></p>
                        <div class="indent xj_friends" style="display:none">
                            <?php $main->dispatch('message', 'friendList', array(array(
                                'friendDataUrl' => $friendDataUrl,
                                'initialFriendSet' => Index_MessageHelper::ALL_FRIENDS,
                                'numFriends' => $numFriendsAcrossNing,
                                'numSelectableFriends' => $numFriendsAcrossNing - $numFriendsOnNetwork,
                                'numSelectableFriendsOnNetwork' => 0,
                                'showSelectAllFriendsLink' => TRUE,
                                'showSelectFriendsOnNetworkLink' => FALSE,
                            ))); ?>
                        </div>
                    <?php
                    } ?>
                </fieldset>
                <p><label for="xj_quickadd_share_msg"><%=xg_html('YOUR_MESSAGE_COLON')%></label><%=xg_html('OPTIONAL_PARENTHASIES')%>
                    <textarea class="textarea wide" rows="3" id="xj_quickadd_invite_msg" name="message"></textarea>
                </p>
                <p class="buttongroup">
                    <a href="<%=qh($main->buildUrl('invitation', 'new'))%>" class="left more_options"><%=xg_html('MORE_OPTIONS')%></a>
                    <input class="button button-primary" name="add" type="submit" value="<%=xg_html('SEND_INVITE')%>">
                    <input class="button" name="cancel" type="button" value="<%=xg_html('CANCEL')%>">
                </p>
            </form>
        </div>
    </div>
</div>
