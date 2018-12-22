<?php
/*  $Id: $
 *
 *  _
 *
 *  Parameters:
 *		$recipientEmail
 *		$senderName
 *		$recipientUser
 *		$code
 *		$recipientText
 *
 */
$url = W_Cache::getWidget('profiles')->buildUrl('profile', 'emailSettings');
XG_App::ningLoaderRequire('xg.shared.PostLink');

$signInUrl = $this->_user->isLoggedIn() ? $url : $this->_buildUrl('authorization', 'signIn',array(
	'target' => $url,
	'emailAddress' => $this->recipientEmail,
));
$blockSenderUrl = $this->_buildUrl('optout', 'blockSender', array('code'=>$this->code));
$blockAllUrl = $this->_buildUrl('optout', 'blockAll', array('code'=>$this->code));
?>
<?php xg_header(null, xg_text('CHANGE_EMAIL_SETTINGS'), null, array('hideNavigation' => true)); ?>
<div id="xg_body">
	<div class="xg_column xg_span-8 xg_prepend-6">
		<%= xg_headline(xg_text('CHANGE_EMAIL_SETTINGS'))%>
		<div class="xg_module">
			<div class="xg_module_body pad options">
				<p><strong><%=xg_html('CHANGE_YOUR_X_EMAIL', qh(XN_Application::load()->name), qh($this->recipientText))%></strong></p>
				<ul>
				<?php if ($this->recipientUser) { ?>
				<li>
					<%=xg_html('CHOOSE_WHAT_EMAILS_YOU_RECEIVE')%><br />
					<strong><a href="<%=qh($signInUrl)%>"><%=xg_html('CHANGE_EMAIL_SETTINGS')%></a></strong>
				</li>
				<?php }?>
				<li>
					<%=xg_html('BLOCK_ALL_EMAILS_FROM_X', qh($this->senderName))%><br />
					<strong><a href="#" dojoType="PostLink"
						_confirmQuestion="<%=xg_html('ARE_YOU_SURE_YOU_WANT_TO_BLOCK_Q', qh($this->senderName))%>"
						_confirmTitle="<%=xg_html('BLOCK')%>"
						_doPromptJoin="0"
						_url="<%=qh($blockSenderUrl)%>"><%=xg_html('Block Sender')%></a></strong>
				</li>
				<li>
					<%=xg_html('STOP_RECEIVING_ALL_EMAILS')%><br />
					<strong><a href="#" dojoType="PostLink"
						_confirmQuestion="<%=xg_html('ARE_YOU_SURE_YOU_WANT_TO_UNSUBSCRIBE_Q', qh(XN_Application::load()->name))%>"
						_confirmTitle="<%=xg_html('UNSUBSCRIBE')%>"
						_doPromptJoin="0"
						_url="<%=qh($blockAllUrl)%>"><%=xg_html('UNSUBSCRIBE')%></a></strong>
				</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php xg_footer(); ?>
