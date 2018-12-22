<?php
XG_IPhoneHelper::header(W_Cache::current('W_Widget')->dir,  $this->topic->title, NULL,  array('metaDescription' => $this->metaDescription, 'metaKeywords' => $this->metaKeywords));
?>
<ul class="list detail forum">
    <?php
    $contributor = XG_Cache::profiles($this->topic->contributorName);
    $href = xnhtmlentities(User::quickProfileUrl($this->topic->contributorName));
    $date = xg_date(xg_text('F_J_Y'), $this->topic->createdDate);
    $time = xg_date(xg_text('G_IA'), $this->topic->createdDate);

    XG_IPhoneHelper::previousPage($this->pageSize);
    ?>
        <li>
            <div class="ib"><a href="<%= $href %>"><img width="48" height="48" alt="" src="<%= xnhtmlentities(XG_UserHelper::getThumbnailUrl($contributor,48,48)) %>"/></a></div>
            <div class="tb">
                <span class="title"><%= xnhtmlentities($this->topic->title) %></span>
                <?php if($this->category){ ?>
                  <span class="metadata"><%= xg_html('POSTED_BY_USER_ON_DATE_AT_TIME_IN_CATEGORY', xg_userlink($contributor,'',FALSE,$href), xnhtmlentities($date), xnhtmlentities($time), 'href="' . xnhtmlentities($this->categoryUrl) . '"', xnhtmlentities($this->category->title)); %></span>
                <?php } else { ?>
                  <span class="metadata"><%= xg_html('POSTED_BY_USER_ON_DATE_AT_TIME', xg_userlink($contributor,'',FALSE,$href), xnhtmlentities($date), xnhtmlentities($time)); %></span>
                <?php } ?>
                <?php
                if ($this->tags) { ?>
                    <span class="lighter"><%= Forum_HtmlHelper::tagHtmlForDetailPage($this->tags); %></span>
                <?php
                }
                ?>
                    <div class="post"><%= xg_nl2br(xg_resize_embeds(xg_shorten_linkText($this->topic->description), 171)) %></div>
                    <?php
                    if (Forum_SecurityHelper::currentUserCanSeeAddCommentLinks($this->topic)) { ?>
                        <p class="buttongroup"><a href="<%= xnhtmlentities($this->_buildUrl('comment', 'new', array('topicId' => $this->topic->id))) %>"><%= xg_html('REPLY_TO_THIS') %></a></p>
                    <?php
                    } ?>

            </div>
        </li>
        <li class="title"  <?php if (!count($this->comments)) { echo 'style="display:none"';} ?>><%= xg_html('REPLIES_TO_THIS_DISCUSSION') %></li>
        <?php
        if (count($this->comments)) { ?>
        <?php
        foreach ($this->comments as $comment) {
            $this->renderPartial('fragment_comment_iphone', 'topic', array('topic' => $this->topic, 'comment' => $comment, 'highlight' => $comment->id == $this->currentCommentId, 'firstPage' => $firstPage, 'lastPage' => $lastPage, 'hasChildComments' => $this->commentIdsWithChildComments[$comment->id], 'threaded' => $this->threadingModel == 'threaded'));
        }
    }
    XG_IPhoneHelper::nextPage($this->showNextLink, $this->pageSize);
    ?>
</ul>
<?php xg_footer(NULL,NULL); ?>