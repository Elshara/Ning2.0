<?php
XG_IPhoneHelper::header(($this->userIsOwner ? 'profile' : 'other_profile'),$this->pageTitle,$this->pageOwner, array('includeActionBarCss' => true, 'metaDescription' => $this->metaDescription, 'user' => $this->user->title));
$details = xg_age_and_location($this->user->title, true, true);
?>
<div class="about">
    <div class="ib">
        <img width="96" height="96" alt="" src="<%= xnhtmlentities(XG_UserHelper::getThumbnailUrl($this->user,96,96)) %>"/>
    </div>
    <div class="tb">
	       	<span class="name"><%= xnhtmlentities(xg_username($this->user->title)) %></span>
		<?php
        if ($this->userAgeSex) { ?>
   			<span class="ageSex"><%= xnhtmlentities($this->userAgeSex) %></span>
   		<?php
        }
        if ($this->userLocation) { ?>
        	<span class="location"><%= xnhtmlentities($this->userLocation) %></span>
		<?php
        }
        if ($this->removeFriendUrl) {?>
        	<span class="friend"><%=xg_html('IS_YOUR_FRIEND')%></span>
        <?php } else if ($this->friendRequestSent) { ?>
        	<span class="friend"><%=xg_html('REQUEST_SENT')%></span>
        <?php }
        if ($this->sendMessageUrl || $this->addFriendUrl || $this->removeFriendUrl) {
        	if ($this->addFriendUrl) { ?>
	        	<form name="add_friend_form" method="post" action="<%= xnhtmlentities($this->addFriendUrl) %>">
	        		<%= XG_SecurityHelper::csrfTokenHiddenInput() %>
	        	</form>
        	<?php
        	}
        	if ($this->removeFriendUrl) { ?>
	        	<form name="remove_friend_form" method="post" action="<%= xnhtmlentities($this->removeFriendUrl) %>">
	        		<%= XG_SecurityHelper::csrfTokenHiddenInput() %>
	        	</form>
        	<?php
        	} ?>
        	<p class="buttongroup">
        	<?php
	        if ($this->sendMessageUrl) { ?>
	        	<a href="<%= xnhtmlentities($this->sendMessageUrl) %>"><%= xg_html('SEND_MESSAGE') %></a>
			<?php
	        }
	        if ($this->addFriendUrl) { ?>
        		<a id="add_friend_link" onclick="void(0)" href="#" _msg="<%= xg_html('ADD_X_AS_A_FRIEND', xnhtmlentities(xg_username($this->user->title))) %>"><%= xg_html('ADD_AS_FRIEND') %></a>
			<?php
			} else if ($this->removeFriendUrl) { ?>
		        <a id="remove_friend_link" onclick="void(0)" href="#" _msg="<%= xg_html('ARE_YOU_SURE_YOU_WANT_TO_REMOVE_X', xnhtmlentities(xg_username($this->user->title))) %>"><%= xg_html('REMOVE_AS_FRIEND') %></a>
			<?php
			} ?>
			</p>
		<?php
        } ?>
    </div>
    <?php
    $this->_widget->dispatch('embed', 'embed1profileqa', array(array('screenName' => $this->user->title, 'maxEmbedWidth'=>266)));
    ?>
</div>

<?php
$this->_widget->dispatch('embed', 'friendsProper', array(array('screenName' => $this->user->title)));
?>

<?php
$_GET['user'] = $this->user->title;
$_GET['sort'] = 'mostRecent';
$_GET['pageSize'] = 10;
$widget = W_Cache::getWidget('forum');
$widget->dispatch('topic', 'listForContributor', array(array('output' => 'embed')));
?>

<?php
$_GET['screenName'] = $this->user->title;
$widget = W_Cache::getWidget('photo');
$widget->dispatch('photo', 'listForContributor', array(array('output' => 'embed')));
?>

<?php
$_GET['attachedTo'] = $this->user->title;
$_GET['attachedToType'] = 'User';
W_Cache::getWidget('profiles')->dispatch('comment', 'list', array(array('output' => 'embed')));
$currentpage = xnhtmlentities(XG_HttpHelper::currentUrl());
?>
<script type="application/x-javascript" src="<%= xg_cdn($this->_widget->buildResourceUrl('js/profile/show_iphone.js')) %>"></script>
<?php xg_footer(NULL,NULL); ?>