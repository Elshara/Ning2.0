<?php XG_IPhoneHelper::header('forum', $this->title, $this->profile, array('contentClass' => 'compose','displayHeader' => false, 'hideNavigation' => true));
$this->_widget->includeFileOnce('/lib/helpers/Forum_HtmlHelper.php');
$post = $this->parentComment ? $this->parentComment : $this->topic;
$contributor = XG_Cache::profiles($post->contributorName);
$href = xnhtmlentities(User::quickProfileUrl($post->contributorName));
$date = xg_date(xg_text('F_J_Y'), $post->createdDate);
$time = xg_date(xg_text('G_IA'), $post->createdDate); ?>

<form id="compose" class="panel" id="add_topic_form" action="<%= xnhtmlentities($this->_buildUrl('comment', 'create', array('parentCommentId' => $this->parentComment->id, 'topicId' => $this->topic->id))) %>" method="post" enctype="multipart/form-data">
    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
	<div id="header">
		<strong><%= xg_html('REPLY') %></strong>
		<a class="title-button" id="add" onclick="javascript:void(0)"><%= xg_html('SUBMIT') %></a>
		<a class="title-button" id="cancel" onclick="javascript:void(0)"><%= xg_html('CANCEL') %></a>
	</div><!--/#header-->
    <%= XG_IPhoneHelper::outputErrors($this->errors, true) %>
    <fieldset>
      	<div class="row">
      		<label for="post"><%= xg_html('REPLY_COLON') %></label>
			<textarea name="description" id="post" class="lighter" _required="<%=qh(xg_html('PLEASE_WRITE_SOMETHING_FOR_REPLY'))%>" _default="<%=qh(xg_html('TAP_HERE_TO_BEGIN_WRITING'))%>"></textarea>
       	</div>
    </fieldset>
</form>
<script>initComposeForm()</script>

<h3><%= xg_html('REPLYING_TO_COLON') %></h3>
<ul class="list detail forum">
   	<li>
   		<div class="ib"><%= xg_avatar($contributor, 48, null, '', true) %></div>
   		<div class="tb">
   		  <span class="title"><a href="<%= xnhtmlentities($this->_buildUrl('topic', 'show', array('id' => $this->topic->id))) %>"><%= qh($this->topic->title) %></a></span>
   		  <?php if($this->category){ ?>
          <span class="metadata"><%= xg_html('POSTED_BY_USER_ON_DATE_AT_TIME_IN_CATEGORY', xg_userlink($contributor,NULL,FALSE,$href), xnhtmlentities($date), xnhtmlentities($time), 'href="' . xnhtmlentities($this->categoryUrl) . '"', xnhtmlentities($this->category->title)); %></span>
        <?php } else { ?>
          <span class="metadata"><%= xg_html('POSTED_BY_USER_ON_DATE_AT_TIME', xg_userlink($contributor,NULL,FALSE,$href), xnhtmlentities($date), xnhtmlentities($time)); %></span>
        <?php } ?>
          <p><%= xg_nl2br(xg_resize_embeds(xg_shorten_linkText($post->description), 171)) %></p>
        </div>
    </li>
</ul>

<?php xg_footer(NULL,array('contentClass' => 'compose', 'regularPage' => $this->_buildUrl('topic','show',array('id'=>$this->topic->id)))); ?>
