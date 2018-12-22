<?php xg_header($this->tab,xg_text('MANAGE_BLOG'), null); ?>
<?php XG_App::ningLoaderRequire('xg.profiles.blog.manage'); ?>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
				<?php XG_PageHelper::subMenu(Profiles_HtmlHelper::blogSubMenu($this->_widget)) ?>
				<%= xg_headline(xg_text('MANAGE_BLOG'))%>
                <div class="xg_module">
                    <div class="xg_module_body pad">
                        <ul class="page_tabs">
                            <li class="this"><span class="xg_tabs"><%= xg_html('MY_POSTS') %></span></li>
                            <li><a href="<%= $this->_buildUrl('blog','manageComments') %>"><%= xg_html('COMMENTS') %></a></li>
                        </ul>
                        <?php
                        if (! count($this->posts)) { ?>
                            <div class="xg_module_body">
                                <p><%= xg_html('YOU_DO_NOT_HAVE_POSTS') %></p>
                            </div>
                        <?php
                        } else { ?>
                            <form method="post" action="<%= $this->_buildUrl('blog','managePosts',array('page' => $this->page)) %>">
                                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                <table summary="<%= xg_html('MY_POSTS') %>" width="100%" class="clear">
                                    <thead>
                                        <tr>
                                            <th scope="col" width="20px"><input type="checkbox" class="checkbox" id="checkbox-top" /></th>
                                            <th scope="col" width="20px"><img src="<%= xg_cdn($this->_widget->buildResourceUrl('gfx/icon/flag.gif')) %>" alt="<%= xg_html('FLAG') %>" /></th>
                                            <th scope="col" width="40%" colspan="2"><%= xg_html('POST_TITLE') %></th>
                                            <th scope="col" width="25%"><%= xg_html('COMMENTS') %></th>
                                            <th scope="col" width="25%"><%= xg_html('DATE') %></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (count($this->posts)) { ?>
                                        <?php foreach ($this->posts as $i => $post) {
                                            $editUrl = xnhtmlentities($this->_buildUrl('blog','edit',array('id' => $post->id)));
                                            if ($post->my->publishStatus == 'publish') {
                                                $date = xg_date(xg_text('M_J_Y_G_IA'), $post->my->publishTime);
                                                $iconClass = 'check';
                                            } elseif ($post->my->publishStatus == 'draft') {
                                                $date = xg_text('DRAFT_PARENTHESES');
                                                $iconClass = 'edit';
                                            } elseif ($post->my->publishStatus == 'queued') {
                                                $iconClass = 'clock';
                                                $date = xg_text('SCHEDULED_TO_PUBLISH_ON', xg_date(xg_text('M_J_Y_G_IA'), $post->my->publishTime));
                                            }
                                            $commentCounts = Comment::getCounts($post);
                                            ?>
                                        <tr <%= (($i % 2) == 1) ? 'class="alt"' : '' %>>
                                            <td><input type="checkbox" class="checkbox" name="id[]" value="<%= $post->id %>" /></td>
                                            <td><a class="icon <%= xnhtmlentities($iconClass) %>" href="<%= $editUrl %>"><%= xnhtmlentities($date) %></a></td>
                                            <td><a href="<%= $editUrl %>"><%= xnhtmlentities(BlogPost::getTextTitle($post)) %></a></td>
                                            <td><a href="<%= $editUrl %>"><%= xg_html('EDIT_2') %></a></td>
                                            <td><?php if ($commentCounts['commentCount']) { ?>
                                                <%= xg_html('N_COMMENTS', $commentCounts['commentCount']) %>
                                            <?php if ($commentCounts['commentToApproveCount']) { ?>
                                            <span class="new"><%= xg_html('N_NEW', $commentCounts['commentToApproveCount']) %>!</span>
                                            <?php } ?>
                                            <?php } else { ?>
                                                <%= xg_html('NO_COMMENTS') %>
                                            <?php } ?></td>
                                            <td><%= xnhtmlentities($date) %></td>
                                        </tr>
                                        <?php } /* each post */ ?>
                                    <?php } else { ?>
                                        <?php /* @todo */?><tr><td colspan="7"><%= xg_html('NO_POSTS') %></td></tr>
                                    <?php } /* posts to display? */ ?>
                                    </tbody>
                                </table>
                                <p>
                                    <input type="submit" class="button" value="<%= qh(xg_html('DELETE')) %>" />
                                </p>
                            </form>
                            <?php
							XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
							XG_PaginationHelper::outputPaginationProper($this->_buildUrl('blog','managePosts'),
								'page', $this->page, $this->numPages);
                        } ?>
                    </div>
                </div>
            </div>
            <div class="xg_1col">
                <div class="xg_1col first-child"><?php xg_sidebar($this); ?></div>
            </div>
        </div>
    </div>
<?php xg_footer(); ?>
