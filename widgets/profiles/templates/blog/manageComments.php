<?php xg_header($this->tab,xg_text('MANAGE_BLOG'), null); ?>
<?php XG_App::ningLoaderRequire('xg.profiles.blog.manage', 'xg.profiles.blog.manageComments') ?>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
				<?php XG_PageHelper::subMenu(Profiles_HtmlHelper::blogSubMenu($this->_widget)) ?>
				<%= xg_headline(xg_text('MANAGE_BLOG'))%>
                <div class="xg_module">
                    <div class="xg_module_body pad">
                        <ul class="page_tabs">
                            <li><a href="<%= $this->_buildUrl('blog','managePosts') %>"><%= xg_html('MY_POSTS') %></a></li>
                            <li class="this"><span class="xg_tabs"><%= xg_html('COMMENTS') %></span></li>
                        </ul>
                        <?php
                        if (! count($this->comments)) { ?>
                            <div class="xg_module_body">
                                <p><%= xg_html('YOU_DO_NOT_HAVE_COMMENTS') %></p>
                            </div>
                        <?php
                        } else { ?>
                            <form method="post" action="<%= $this->_buildUrl('blog','manageCommentsSubmit',array('page' => $this->page)) %>">
                                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                <table summary="<%= xg_html('COMMENTS_POSTED_ON_BLOG') %>" width="100%" class="clear">
                                    <thead>
                                        <tr>
                                            <th scope="col"><input type="checkbox" class="checkbox" id="checkbox-top" /></th>
                                            <th scope="col" width="45%"><%= xg_html('COMMENT') %></th>
                                            <th scope="col" width="15%"><%= xg_html('COMMENTER') %></th>
                                            <th scope="col" width="20%"><%= xg_html('POST') %></th>
                                            <th scope="col" width="15%"><%= xg_html('DATE') %></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (count($this->comments)) { ?>
                                            <?php
                                            foreach ($this->comments as $i => $comment) {
                                                $classes = array();
                                                if ($comment->my->approved == 'N') {
                                                    $classes[] = 'notification';
                                                }
                                                if (($i % 2) == 1) {
                                                    $classes[] = 'alt';
                                                }
                                                if (count($classes)) {
                                                    $classString = ' class="' . implode(' ', $classes) .'"';
                                                } else {
                                                    $classString = '';
                                                }
                                                 $profile = $this->commentContributors[$comment->contributorName];
                                                 $post = $this->posts[$comment->my->attachedTo];
                                                 if (mb_strlen($comment->description) > $this->truncateCommentAt) {
                                                   $firstPart = xg_nl2br(xnhtmlentities(mb_substr($comment->description, 0, $this->truncateCommentAt)));
                                                   $secondPart = xg_nl2br(xnhtmlentities(mb_substr($comment->description, $this->truncateCommentAt)));
                                                   $commentText = $firstPart.'<span id="comment-more-container-'.$comment->id.'"> <a class="comment-more-link" id="comment-more-link-'.$comment->id.'" href="#"> ' .  xg_html('MORE_ELLIPSIS') . '</a></span><span style="display: none" id="comment-more-'.$comment->id.'">'.$secondPart.' [<a class="comment-less-link" id="comment-less-link-'.$comment->id.'" href="#">x</a>]</span>';
                                                 } else {
                                                   $commentText = xg_nl2br(xnhtmlentities($comment->description));
                                                 }
                                                $date = xg_date(xg_text('M_J_Y_G_IA'), $comment->createdDate);
                                                ?>
                                            <tr<%= $classString %>>
                                                <td><input type="checkbox" class="checkbox" name="id[]" value="<%= $comment->id %>" /></td>
                                                <td><%= $commentText %></td>
                                                <td class="vcard"><img class="photo" src="<%= XG_UserHelper::getThumbnailUrl($profile,14,14) %>" alt="<%= xg_html('XS_PHOTO', xnhtmlentities(xg_username($profile))) %>" width="14" height="14" /><%= xg_userlink($profile) %></td>
                                                <td>
                                                    <?php
                                                    if ($post instanceof XN_Content) {
                                                        echo '<a href="'.xnhtmlentities($this->_buildUrl('blog','show',array('id' => $post->id))).'">'.xnhtmlentities(BlogPost::getTextTitle($post)).'</a>';
                                                    } else {
                                                        echo xg_html('POST_HAS_BEEN_DELETED');
                                                    } ?>
                                                </td>
                                                <td><%= xnhtmlentities($date) %></td>
                                            </tr>
                                            <?php
                                            } ?>
                                        <?php
                                        } else { ?>
                                            <tr><td colspan="5"><%= xg_html('NO_COMMENTS') %></td></tr>
                                        <?php
                                        } /* comments? */ ?>
                                    </tbody>
                                </table>
                                <input type="hidden" name="comment_action" id="comment_action" value="" />
                                <p>
									<input type="submit" id="comment_action_approve" class="button" value="<%= xg_html('APPROVE') %>" />
                                    <input type="submit" id="comment_action_delete" class="button" value="<%= xg_html('DELETE') %>" />
                                </p>
                            </form>
                            <?php
							XG_App::includeFileOnce('/lib/XG_PaginationHelper.php');
							XG_PaginationHelper::outputPaginationProper($this->_buildUrl('blog','manageComments'),
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
