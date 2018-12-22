<?php if ($this->questionsAndAnswers) { ?>
<ul class="list detail wall">
    <li class="section"><%= xg_html('PROFILE') %></li>
    <?php foreach ($this->questionsAndAnswers as $question => $answer) { ?>
        <?php if ($answer['private']) { ?>
            <?php if($this->canSeePrivate) { ?>
                <li>
            <dl title="<%= xg_html('PRIVATE_QUESTION')%>" class="private">
                <dt><%= $question %></dt>
                <dd><%= xg_nl2br(xg_resize_embeds(xg_shorten_linkText($answer['answer']), $this->maxEmbedWidth)) %></dd>
            </dl>
            </li>
            <?php } ?>
        <?php } else { ?>
            <li>
            <dl>
                <dt><%= $question %></dt>
                <dd><%= xg_nl2br(xg_resize_embeds(xg_shorten_linkText($answer['answer']), $this->maxEmbedWidth)) %></dd>
            </dl>
            </li>
        <?php } ?>
    <?php } ?>
</ul>
<?php } ?>