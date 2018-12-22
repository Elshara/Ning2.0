<?php
$main = W_Cache::getWidget('main');
$opensocial = W_Cache::getWidget('opensocial');

$opensocial->includeFileOnce('/lib/helpers/OpenSocial_PersonHelper.php');

$friendDataUrl = XG_SecurityHelper::addCsrfToken($this->_buildUrl('message', 'friendData', array('appUrl' => $appUrl, 'ownerId' => $ownerId, 'viewerId' => $viewerId, 'ids' => join(',',$ids), 'xn_out' => 'json')));
?>
<div class="xg_floating_module" style="display:none">
    <div class="xg_floating_container xg_floating_container_wide xg_module">
        <div class="xg_module_head">
            <h2><%=xg_html('SEND_MESSAGE')%></h2>
        </div>
        <div class="xg_module_body">
            <p>
                <?php if ($showFriends) { ?>
                    <%=xg_html('APP_WOULD_LIKE_TO_SEND_MSG',xnhtmlentities($appTitle))%>
                <?php } else { $friends = $this->people; $friend = $friends[0]; ?>
                    <%=xg_html('APP_WOULD_LIKE_TO_SEND_MSG_TO_X',xnhtmlentities($appTitle),xnhtmlentities($friend['name']))%>
                <?php } ?>
                <blockquote><strong><em><%=$subject%></em></strong><br/><%=$message%></blockquote>
            </p>
            <form id="xg_quickadd_sendmessageform" action="<%=$opensocial->buildUrl('message','sendQuick','?xn_out=json&appUrl='.$appUrl.'&viewerId='.$viewerId.'&ownerId='.$ownerId.'&msgType='.$msgType)%>" method="post">
                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                <div class="msg success" id="xg_quickadd_sendmessageform_notify_success" style="display:none"></div>
                <div class="msg" id="xg_quickadd_sendmessageform_notify" style="display:none"></div>
                <fieldset class="nolegend last-child">
                    <?php if ($showFriends) { ?>
                        <%=xg_html('SELECT_RECIPIENTS_FROM_MY')%>
                    <?php } ?>
                    <input type="hidden" name="ids" value="<%=join(',',$ids)%>" />
                    <input type="hidden" name="subject" value="<%=xnhtmlentities($subject)%>" />
                    <input type="hidden" name="message" value="<%=xnhtmlentities($message)%>" />
                    <input type="hidden" name="numFriends" value="<%=$this->numUsers%>" />
                    <div class="indent xj_friends" <%= $showFriends ? "" : "style=\"display:none\"" %>>
                        <?php
                        $main->includeFileOnce('/lib/helpers/Index_MessageHelper.php');
                        $main->dispatch('message', 'friendList', array(array(
                            'initialFriendSet' => Index_MessageHelper::FRIENDS_ON_NETWORK,
                            'friendDataUrl' => $friendDataUrl,
                            'numFriends' => $showFriends ? $this->numUsers : 0,
                            'numSelectableFriends' => $showFriends ? $this->numUsers : 0,
                            'numSelectableFriendsOnNetwork' => $showFriends ? $this->numUsers : 0,
                            'showSelectAllFriendsLink' => FALSE,
                            'showSelectFriendsOnNetworkLink' => TRUE,
                        ))); ?>
                    </div>
                    <input type="checkbox" value="1" name="dontPromptBeforeSending" checked="checked" /> <strong><%=xg_html('ALWAYS_ALLOW_APP_TO_SEND_MSG', xnhtmlentities($appTitle))%></strong>
                </fieldset>
                <p class="buttongroup">
                    <input class="button button-primary" name="add" type="submit" value="<%=xg_html('SEND_MESSAGE')%>">
                    <input class="button" name="cancel" type="button" value="<%=xg_html('CANCEL')%>">
                </p>
            </form>
        </div>
    </div>
</div>
