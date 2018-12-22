<?php
xg_header($this->tab, $this->pageTitle, null, array('metaKeywords' => $this->metaKeywords, 'metaDescription' => $this->metaDescription)); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
		<?php XG_PageHelper::subMenu(Profiles_HtmlHelper::blogSubMenu($this->_widget)) ?>
		<%= xg_headline(strip_tags($this->titleHtml), array('count' => $this->posts['numPosts'], 'avatarUser' => $this->user))%>
        <?php
        if ($this->posts['numPosts'] == 0 || $_GET['test_empty']) {
        	XG_PageHelper::searchBar(array('url' => '', 'buttonText' => xg_html('SEARCH_BLOG_POSTS')));
        	?>
            <div class="xg_module_body">
                <?php
                if ($this->userIsOwner) { ?>
                  <div class="adminbox xg_module xg_span-4 adminbox-right">
                      <div class="xg_module_head">
                          <h2><%= xg_html('ADMIN_OPTIONS') %></h2>
                      </div>
                      <div class="xg_module_body">
                          <ul class="nobullets last-child">
                            <li><a class="desc settings" href="<%= xnhtmlentities($this->_buildUrl('blog','managePosts')) %>"><%= xg_html('MANAGE_BLOG') %></a></li>
                          </ul>
                      </div>
                  </div>
                <?php
                }
                if ($this->noPostsMessageHasAddLink) { echo '<h3>' . xg_html('ADD_BLOG_POST') . '</h3>'; }

                echo '<p>' . xnhtmlentities($this->noPostsMessage) . '</p>';
                if ($this->noPostsMessageHasAddLink) { echo '<p><a href="' . xnhtmlentities($this->_buildUrl('blog', 'new')) . '" class="bigdesc add">' . xg_html('ADD_BLOG_POST') . '</a></p>'; } ?>
            </div>
        <?php
        } else { ?>
            <div class="xg_column xg_span-12">
            	<?php XG_PageHelper::searchBar(array('url' => '', 'buttonText' => xg_html('SEARCH_BLOG_POSTS'))); ?>
                <div class="xg_module xg_blog xg_blog_list <%= $this->user ? 'xg_blog_mypage' : null %>">
                    <?php
                    $n = count($this->posts['posts']);
                    for ($i = 0; $i < $n; $i++) {
                        $post = $this->posts['posts'][$i];
                        $contributor = XG_Cache::profiles($post->contributorName); ?>
                        <div class="xg_module_body">
                            <h3 class="title">
                                <%= $this->user ? '' : xg_avatar($contributor, 48) %>
                                <a href="<%= $this->_buildUrl('blog','show',array('id' => $post->id)) %>"><%= BlogPost::getHtmlTitle($post) %></a>
                            </h3>
                            <div class="postbody">
                              <%= xg_resize_embeds(BlogPost::summarize($post), 545) %>
                              <?php
                              if ($summary != $post->description || mb_strlen($post->title) == 0 || mb_strlen($post->description) > 545) { ?><a href="<%= xnhtmlentities($this->_buildUrl('blog','show',array('id' => $post->id))) %>"><%= xg_html('CONTINUE') %></a>
                              <?php
                              } ?>
                            </div>
                            <p class="small">
                                    <?php
                                    $date = xg_date(xg_text('F_J_Y'), $post->my->publishTime);
                                    $time = xg_date(xg_text('G_IA'), $post->my->publishTime); ?>
                                    <%= xg_html('POSTED_BY_USER_ON_DATE_AT_TIME', xg_userlink($contributor), xnhtmlentities($date), xnhtmlentities($time)) %>
                                    —
                                    <?php
                                    $commentCounts = Comment::getCounts($post);
                                    if ($commentCounts['approvedCommentCount']) { ?>
                                        <a href="<%= $this->_buildUrl('blog','show',array('id' => $post->id)) %>#comments"><%= xg_html('N_COMMENTS', $commentCounts['approvedCommentCount']) %></a>
                                    <?php
                                    } else { ?>
                                        <%= xg_html('NO_COMMENTS') %>
                                    <?php
                                    } ?>
                            </p>
                            <?php
                            ob_start();
                            W_Cache::getWidget('main')->dispatch('promotion','link',array($post, 'post'));
                            $featureLink = trim(ob_get_contents());
                            ob_end_clean();
                            if ($featureLink) { echo '<p class="small">' . $featureLink . '</p>'; }
                            if ($this->previousPageUrl || $this->nextPageUrl) {
                                echo '<ul class="pagination smallpagination">';
                                    if ($this->previousPageUrl) { ?>
                                        <li class="left"><a href="<%= xnhtmlentities($this->previousPageUrl) %>"><%= xg_html('PREVIOUS_POST') %></a></li>
                                    <?php
                                    }
                                    if ($this->nextPageUrl) { ?>
                                        <li class="right"><a href="<%= xnhtmlentities($this->nextPageUrl) %>"><%= xg_html('NEXT_POST') %></a></li>
                                    <?php
                                    }
                                echo '</ul>';
                            } ?>
                        </div>
                    <?php
                    }
                    if (! XG_App::appIsPrivate() && $this->feedUrl) {
                        xg_autodiscovery_link($this->feedUrl, $title, 'atom'); ?>
                        <div class="xg_module_foot">
                            <ul>
                                <li class="left"><a class="desc rss" href="<%= xnhtmlentities($this->feedUrl) %>"><%= xg_html('RSS') %></a></li>
                            </ul>
                        </div>
                    <?php
                    } ?>
                </div>
            </div>
            <div class="xg_column xg_span-4 xg_last">
                <?php
                if ($this->userIsOwner) { ?>
                    <div class="xg_module adminbox">
                        <div class="xg_module_head">
                            <h2><%= xg_html('ADMIN_OPTIONS') %></h2>
                        </div>
                        <div class="xg_module_body">
                            <ul class="nobullets last-child">
                                <li><a class="desc settings" href="<%= xnhtmlentities($this->_buildUrl('blog','managePosts')) %>"><%= xg_html('MANAGE_BLOG') %></a></li>
                            </ul>
                        </div>
                    </div>
                <?php
                }

                if ($this->showFeaturedBlock && $this->featuredPosts['numPosts']) {
                    $this->renderPartial('fragment_featured', array('title' => xg_html('FEATURED_BLOG_POSTS'), 'posts' => $this->featuredPosts['posts'], 'numPosts' => $this->featuredPosts['numPosts']));
                }
                if ($this->recentPosts['numPosts']) {
                    $this->renderPartial('fragment_posts', array('title' => $this->latestBlogPostsTitle, 'posts' => $this->recentPosts['posts']));
                }
                if ($this->popularPosts['numPosts']) {
                    $this->renderPartial('fragment_posts', array('title' => $this->mostPopularBlogPostsTitle, 'posts' => $this->popularPosts['posts']));
                }
                if ($this->tags) {
                    $this->renderPartial('fragment_tagList', array('tags' => $this->tags, 'screenName' => $this->user ? $this->user->title : null));
                }
                if ($this->archive) {
                    $this->renderPartial('fragment_archive', array('title' => $this->monthlyArchivesTitle, 'archive' => $this->archive, 'user' => $this->user->contributorName, 'promoted' => $this->promoted));
                } ?>
            </div>
        <?php
        } ?>
    </div>
    <div class="xg_column xg_span-4 xg_last">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
