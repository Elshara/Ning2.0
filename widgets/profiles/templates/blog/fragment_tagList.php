<?php
/**
 * Displays a list of tags and their counts
 *
 * @param $screenName string  A username to filter on (optional)
 * @param $tags array  The tags as an array of tagname => popularity
 */ ?>
<div class="xg_module">
    <div class="xg_module_head">
        <h2><%= xg_html('BLOG_TOPICS_BY_TAGS') %></h2>
    </div>
    <div class="xg_module_body">
            <ul class="nobullets">
                <?php
                foreach ($tags as $tag => $count) { ?>
                    <li><%= xg_html('TAG_COUNT', 'rel="tag" href="' . xnhtmlentities($this->_buildUrl('blog', 'list', array('tag' => $tag, 'user' => $screenName))) . '"', xnhtmlentities($tag), $count) %></li>
                <?php
                } ?>
            </ul>
    </div>
</div>
