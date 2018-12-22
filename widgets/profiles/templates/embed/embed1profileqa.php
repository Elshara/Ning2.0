<?php if ($this->questionsAndAnswers || $this->embed->isOwnedByCurrentUser()) { ?>
<div class="xg_module module_about_user">
    <div class="xg_module_head">
		<h2><%= xg_html('PROFILE_INFORMATION') %></h2>
                <?php if ($this->embed->isOwnedByCurrentUser()) { ?>
                    <p class="edit">
                        <a class="button" href="<%= xnhtmlentities($this->_buildUrl('settings','editProfileInfo')) %>"><%= xg_html('EDIT') %></a>
                    </p>
                <?php } ?>
    </div>
    <?php if ($this->questionsAndAnswers) { ?>
        <div class="xg_module_body">
    <?php } ?>
<?php /* TODO: Eliminate code duplication below [Jon Aquino 2008-02-29] */ ?>
<?php foreach ($this->questionsAndAnswers as $question => $answer) { ?>
    <?php if ($answer['private']) { ?>
        <?php if($this->canSeePrivate) { ?>
        <dl title="Private Question" class="private">
            <dt><%= $question %></dt>
            <dd><%= xg_nl2br(xg_resize_embeds(xg_shorten_linkText($answer['answer']), $this->maxEmbedWidth)) %></dd>
        </dl>
        <?php } ?>
    <?php } else { ?>
        <dl>
            <dt><%= $question %></dt>
            <dd><%= xg_nl2br(xg_resize_embeds(xg_shorten_linkText($answer['answer']), $this->maxEmbedWidth)) %></dd>
        </dl>
    <?php } ?>
<?php } ?>
    <?php if ($this->questionsAndAnswers) { ?>
        </div>
    <?php } ?>
    <?php if (!$this->questionsAndAnswers && $this->embed->isOwnedByCurrentUser()) { ?>
        <div class="xg_module_foot">
            <ul>
                <li class="left"><a class="desc add" href="<%= xnhtmlentities($this->_buildUrl('settings','editProfileInfo')) %>"><%= xg_html('ADD_PROFILE_INFORMATION') %></a></li>
            </ul>
        </div>
    <?php } ?>
</div>
<?php } ?>