<?php
/** Show the blog posts in a sidebar module
 *
 * @param $title string  text for the module heading
 * @param $posts array  BlogPost content objects
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
</div>

