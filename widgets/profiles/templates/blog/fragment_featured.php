<?php
/** Show the featured blog posts in a sidebar module
 *
 * @param $title string  text for the module heading
 * @param $posts array  BlogPost content objects
 * @param $numPosts number the number of posts
 */ ?>
 <div class="xg_module">
    <div class="xg_module_head">
        <h2><%= xnhtmlentities($title) %></h2>
    </div>
    <div class="xg_module_body">
        <ul class="nobullets">
            <?php
            foreach ($posts as $post) { ?>
                <li><a href="<%= xnhtmlentities($this->_buildUrl('blog', 'show', array('id' => $post->id))) %>"><%= xnhtmlentities(BlogPost::getTextTitle($post)) %></a></li>
            <?php
            } ?>
        </ul>
    </div>
    <?php if ($numPosts > 7) {?>
        <div class="xg_module_foot">
            <p class="right"><a href="<%= xnhtmlentities($this->_buildUrl('blog', 'list', array('promoted' => 1))) %>"><%= xg_html('VIEW_ALL') %></p>
        </div>
    <?php } ?>
</div>

