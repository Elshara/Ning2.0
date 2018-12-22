<?php
/*  $Id: $
 *
 */
$main = W_Cache::getWidget('main');

$main->includeFileOnce('/lib/helpers/Index_MessageHelper.php');

$numFriendsAcrossNing = Index_MessageHelper::numberOfFriendsAcrossNing($this->_user->screenName);
$numFriendsOnNetwork = XG_App::constant('Index_MessageHelper::QUICKADD_SHARE_FRIENDS_ON_NETWORK_CHECKBOX_DISPLAYED') ? Index_MessageHelper::numberOfFriendsOnNetwork($this->_user->screenName) : 0;
$friendDataUrl = $this->_buildUrl('sharing', 'friendData', array('xn_out' => 'json'))
?>
<div class="xg_floating_module" style="display:none">
    <div class="xg_floating_container xg_floating_container_wide xg_module">
        <div class="xg_module_head">
            <h2><span class="png-fix"><img src="<%=xg_cdn('/xn_resources/widgets/index/gfx/icon/quickpost/share.png')%>" alt="" /></span><%=xg_html('SHARE_THIS_PAGE')%></h2>
        </div>
        <div class="xg_module_body">
            <form id="xg_quickadd_share" action="<%=$main->buildUrl('sharing','shareQuick','/.txt?xn_out=json')%>" method="post">
                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                <input type="hidden" name="url" value="">
                <input type="hidden" name="title" value="">
                <input type="hidden" name="contentId" value="">
                <div class="msg" id="xg_quickadd_share_notify" style="display:none"></div>
                <fieldset class="nolegend">
                    <p>
                        <label><%=xg_html('SHARE_ON_COLON')%></label>
                        <br/>
                        <span class="block left xg_append-2">
                            <a class="desc service-myspace" href="#" target="_blank"><%=xg_html('MYSPACE')%></a><br/>
                            <a class="desc service-twitter" href="#" target="_blank"><%=xg_html('TWITTER')%></a><br/>
                            <a class="desc service-delicious" href="#" target="_blank"><%=xg_html('DEL_ICIO_US')%></a><br/>
                        </span>
                        <span class="block left">
                            <a class="desc service-facebook" href="#" target="_blank"><%=xg_html('FACEBOOK')%></a><br/>
                            <a class="desc service-stumbleupon" href="#" target="_blank"><%=xg_html('STUMBLEUPON')%></a><br/>
                            <a class="desc service-digg" href="#" target="_blank"><%=xg_html('DIGG')%></a><br/>
                        </span>
                    </p>
                </fieldset>
                <fieldset class="nolegend last-child">
                    <p>
                        <label><%=xg_html('OR_SEND_TO_COLON')%><input type="text" name="emailAddresses" class="textfield wide" /></label><br />
                        <small><%=xg_html('ENTER_EMAIL_ADDRESSES_SEPARATE')%></small><br />
                    </p>
                    <?php if ($numFriendsAcrossNing) {?>
                    <p class="toggle xj_toggle"><a href="#"><span>&#9658;</span><%=xg_html('SELECT_RECIPIENTS_FROM_MY')%></a></p>
                    <div class="indent xj_friends" style="display:none">
                        <?php $main->dispatch('message', 'friendList', array(array(
                            'friendDataUrl' => $friendDataUrl,
                            'initialFriendSet' => Index_MessageHelper::ALL_FRIENDS,
                            'numFriends' => $numFriendsAcrossNing,
                            'numSelectableFriends' => $numFriendsAcrossNing,
                            'numSelectableFriendsOnNetwork' => $numFriendsOnNetwork,
                            'showSelectAllFriendsLink' => TRUE,
                            'showSelectFriendsOnNetworkLink' => XG_App::constant('Index_MessageHelper::QUICKADD_SHARE_FRIENDS_ON_NETWORK_CHECKBOX_DISPLAYED'),
                        ))); ?>
                    </div>
                    <?php }?>
                </fieldset>
                <p><label for="xj_quickadd_share_msg"><%=xg_html('YOUR_MESSAGE_COLON')%></label><%=xg_html('OPTIONAL_PARENTHASIES')%>
                    <textarea class="textarea wide" rows="2" id="xj_quickadd_share_msg" name="message"></textarea>
                </p>
                <p class="buttongroup">
                    <input class="button button-primary" name="add" type="submit" value="<%=xg_html('SHARE_THIS_PAGE')%>">
                    <input class="button" name="cancel" type="button" value="<%=xg_html('CANCEL')%>">
                </p>
            </form>
        </div>
    </div>
</div>
