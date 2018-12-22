<?php
xg_header('manage', xg_text('XS_PAGE', xg_username($this->profile)));
// is xg.index.actionicons needed here? [ywh 2008-06-25]
XG_App::ningLoaderRequire('xg.index.actionicons','xg.shared.util','xg.index.bulk','xg.index.membership.list');
?>
<div id="xg_body">
    <div class="xg_column xg_span-20">
      <%=xg_headline(xg_username($this->profile), array('byline1Html' => '<a href="' . qh($this->listPendingUrl) . '">' . xg_html('LARR_BACK_TO_PENDING_MEMBERS') . '</a>'))%>
        <div class="xg_column xg_span-16">
          <div class="xg_module">
            <form method="post" id="xg_member_form_1" action="<%= xnhtmlentities(W_Cache::getWidget('main')->buildUrl('membership','savePending')) %>" >
              <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
              <div class="xg_module_body notification">
                <p><%= xg_html('X_IS_AWAITING_APPROVAL_TO_BECOME_MEMBER_OF_Y', xnhtmlentities(xg_username($this->profile)), xnhtmlentities(XN_Application::load()->name)) %></p>
                <p class="buttongroup">
                  <a id="ban_button_1" dojoType="BulkActionLink"
                    title="<%= xg_html('BAN_FROM_NETWORK') %>"
                    _url="<%= xnhtmlentities(W_Cache::getWidget('main')->buildUrl('bulk','removeByUser', array('user' => $this->profile->screenName, 'xn_out' => 'json'))) %>"
                    _verb="<%= xg_html('BAN') %>"
                    _confirmMessage="<%= xg_html('ARE_YOU_SURE_BAN_MEMBERS') %>"
                    _progressTitle="<%= xg_html('REMOVING_MEMBERS') %>"
                    _progressMessage="<%= xg_html('KEEP_WINDOW_OPEN_MEMBERS_DELETED') %>"
                    _successUrl="<%= xnhtmlentities($this->listPendingUrl) %>" href="#" class="button">
                    <%= xg_html('BAN_FROM_NETWORK') %></a>
                  <input type="hidden" name="operation" value="" />
                  <input type="hidden" name="page" value="<%= xnhtmlentities($this->page) %>" />
                  <input type="hidden" name="user_<%= xnhtmlentities($this->profile->screenName) %>" value="1" />
                  <a href="javascript:xg.index.membership.list.submitWithOp('decline','xg_member_form_1')" class="button"><%= xg_html('DECLINE') %></a>
                  <a href="javascript:xg.index.membership.list.submitWithOp('accept','xg_member_form_1')" class="button button-primary"><%= xg_html('ACCEPT') %></a>
                </p>
              </div>
            </form>
          </div>
          <div class="xg_column xg_span-4">
            <div class="xg_module">
              <div class="xg_module_body">
                <fieldset class="nolegend">
                  <img class="photo left" style="margin-right:7px" src="<%= XG_UserHelper::getThumbnailUrl($this->profile,64,64) %>" alt="<%= xnhtmlentities(xg_username($this->profile)) %>" />
                  <p class="last-child clear">
                    <strong><%= xnhtmlentities(xg_username($this->profile)) %></strong><br />
                    <?php
$additionalInfo = '';
$line = array();
if (XG_UserHelper::getAge($this->profile)) {
    $line[] = XG_UserHelper::getAge($this->profile);
}
$gender = XG_UserHelper::getGender($this->profile);
if ($gender) {
    $line[] = ($gender == 'f') ? xg_html('FEMALE') : xg_html('MALE');
}
if (count($line)) {
    $additionalInfo .= implode(', ', $line) . '<br />';
}
$location = XG_UserHelper::getLocation($this->profile);
if ($location) {
    $additionalInfo .= xnhtmlentities($location) . '<br />';
}
$country = XG_UserHelper::getCountry($this->profile);
if ($country) {
    $additionalInfo .= (($country == 'US') ? xg_html('UNITED_STATES') : xnhtmlentities($country)) . '<br />';
}
                    ?>
                    <%= $additionalInfo %>
                    <%= xg_send_message_link($this->profile->screenName, null) %>
                  </p>
                </fieldset>
              </div>
            </div>
          </div>
          <div class="xg_column xg_span-12 xg_last">
            <div class="xg_module">
              <div class="xg_module_body">
                <h3><%= xg_html('ABOUT_X', xnhtmlentities(xg_username($this->profile))) %></h3>
                <dl>
                <?php foreach ($this->questionsAndAnswers as $question => $answer) { ?>
                    <dt><%= $question . ($answer['private'] ? '<small class="private">'.xg_html('PRIVATE').'</small>' : '') %></dt>
                    <dd><%= xg_nl2br(xg_resize_embeds(xg_shorten_linkText($answer['answer']), 530)) %></dd>
                <?php } ?>
                </dl>
              </div>
            </div>
           <form method="post" id="xg_member_form_2" action="<%= xnhtmlentities(W_Cache::getWidget('main')->buildUrl('membership','savePending')) %>" >
            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
            <p class="buttongroup">
              <a id="ban_button_2" dojoType="BulkActionLink"
              title="<%= xg_html('BAN_FROM_NETWORK') %>"
              _url="<%= xnhtmlentities(W_Cache::getWidget('main')->buildUrl('bulk','removeByUser', array('user' => $this->profile->screenName, 'xn_out' => 'json'))) %>"
              _verb="<%= xg_html('BAN') %>"
              _confirmMessage="<%= xg_html('ARE_YOU_SURE_BAN_MEMBERS') %>"
              _progressTitle="<%= xg_html('REMOVING_MEMBERS') %>"
              _progressMessage="<%= xg_html('KEEP_WINDOW_OPEN_MEMBERS_DELETED') %>"
              _successUrl="<%= xnhtmlentities($this->listPendingUrl) %>" href="#" class="button">
              <%= xg_html('BAN_FROM_NETWORK') %></a>
              <input type="hidden" name="operation" value="" />
              <input type="hidden" name="page" value="<%= xnhtmlentities($this->page) %>" />
              <input type="hidden" name="user_<%= xnhtmlentities($this->profile->screenName) %>" value="1" />
              <a href="javascript:xg.index.membership.list.submitWithOp('decline','xg_member_form_2')" class="button"><%= xg_html('DECLINE') %></a>
              <a href="javascript:xg.index.membership.list.submitWithOp('accept','xg_member_form_2')" class="button button-primary"><%= xg_html('ACCEPT') %></a>
            </p>
           </form>
          </div>
        </div>
        <div class="xg_column xg_span-4 xg_last">
          <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
