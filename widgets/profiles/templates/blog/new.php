<?php xg_header($this->tab,$this->pageTitle, null); ?>
<?php XG_App::ningLoaderRequire('xg.profiles.blog.new', 'xg.shared.SimpleToolbar'); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Profiles_HtmlHelper::blogSubMenu($this->_widget, $this->editExistingPost ? 'post' : 'none')) ?>
			<%= xg_headline($this->pageHeadline)%>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module" style="z-index:2">
                        <div class="xg_module_body pad">
                            <dl id="post_form_notify" style="display: none"></dl>
                            <form id="post_form" action="<%= xnhtmlentities($this->formAction) %>" method="post">
                                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                <fieldset class="nolegend">
                                    <p>
                                        <label for="post_title"><%= xg_html('POST_TITLE') %></label>
                                        <%= $this->form->text('post_title','id="post-title" class="textfield" size="64" style="width:98%"') %>
                                    </p>
                                    <p class="last-child">
                                        <label for="post_body"><%= xg_html('ENTRY') %></label></p>
                                    <div class="texteditor normal"><%= $this->form->textarea('post_body', 'id="post_body" rows="25" cols="105" style="width:98%" dojoType="SimpleToolbar"'); %></div>
                                    <p>
                                        <label for="tags"><%= xg_html('TAGS') %></label>
                                        <?php XG_App::includeFileOnce('/lib/XG_TagHelper.php'); ?>
                                        <%= $this->form->text('tags','id="tags" class="textfield" style="width:95%;" maxlength="' . XG_TagHelper::MAX_TAGS_LENGTH . '"') %>
                                        <?php XG_App::ningLoaderRequire('xg.shared.ContextHelpToggler'); ?>
                                        <span class="context_help"><a dojoType="ContextHelpToggler" href="#"><img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/icon/help.gif')) %>" alt="?" title="<%= xg_html('WHAT_IS_THIS') %>" /></a>
                                            <span class="context_help_popup" style="display:none">
                                                <span class="context_help_content">
                            <%= xg_html('TAGS_ARE_SHORT_DESCRIPTIONS_POST') %>
                                                    <small><a dojoType="ContextHelpToggler" href="#"><%= xg_html('CLOSE') %></a></small>
                                                </span>
                                            </span>
                                        </span>
                                    </p>
                                    <fieldset>
                                        <div class="legend"><%= xg_html('WHAT_DATE_FOR_YOUR_POST') %></div>
                                        <ul class="options">
                                            <li><label for="post_when_now"><%= $this->form->radio('post_when','now','id="post_when_now" class="radio"') %><%= xg_html('NOW') %></label></li>
                                            <li><label for="post_when_later"><%= $this->form->radio('post_when','later','id="post_when_later" class="radio"') %><%= xg_html('CHOOSE_DATE_AND_TIME') %></label><br />
                                                <div style="margin-left:20px" class="<%= xnhtmlentities($this->post_when_timedate_class) %>" id="post_when_timedate">
                                                <%= xg_html('DATE_COLON') %>
                                                    <%= $this->form->select('post_month', $this->months, FALSE, 'onChange="dojo.byId(\'post_when_later\').click()"'); %>
                                                    <%= $this->form->text('post_day', 'class="textfield" size="2" maxlength="2" onChange="dojo.byId(\'post_when_later\').click()"'); %>
                                                    <%= $this->form->text('post_year', 'class="textfield" size="4" maxlength="4" onChange="dojo.byId(\'post_when_later\').click()"'); %>&#160;&#160;
                                                    <span style="white-space:nowrap;">
                                                        <%= xg_html('TIME_COLON') %>
                                                        <%= $this->form->select('post_hour', $this->hours, FALSE, 'onChange="dojo.byId(\'post_when_later\').click()"'); %>
                                                        <%= $this->form->select('post_min', $this->minutes, FALSE, 'onChange="dojo.byId(\'post_when_later\').click()"'); %>
                                                        <%= $this->form->select('post_ampm', $this->ampm, FALSE, 'onChange="dojo.byId(\'post_when_later\').click()"'); %>
                                                        <%= xnhtmlentities($this->timezone) %>
                                                    </span>
                                                </div>
                                            </li>
                                        </ul>
                                    </fieldset>
                                </fieldset>
                                <fieldset>
                                    <div class="legend"><%= xg_html('PRIVACY_AND_COMMENTS') %></div>
                                    <fieldset>
                                        <div class="legend"><%= xg_html('WHO_CAN_VIEW_POST') %></div>
                                        <p>
                                            <label for="post_privacy_all"><%= $this->form->radio('post_privacy','all','class="radio" id="post_privacy_all"') %><%= xg_html('EVERYONE') %></label>
                                            <label for="post_privacy_friends"><%= $this->form->radio('post_privacy','friends','class="radio" id="post_privacy_friends"') %><%= xg_html('JUST_MY_FRIENDS') %></label>
                                            <label for="post_privacy_me"><%= $this->form->radio('post_privacy','me','class="radio" id="post_privacy_me"') %><%= xg_html('JUST_ME') %></label>
                                        </p>
                                    </fieldset>
                                    <fieldset>
                                        <div class="legend"><%= xg_html('WHO_CAN_COMMENT_ON_POST') %></div>
                                        <p>
                                            <label><%= $this->form->radio('post_add_comment_permission','all','class="radio"') %><%= xg_html('EVERYONE') %></label>
                                            <label><%= $this->form->radio('post_add_comment_permission','friends','class="radio"') %><%= xg_html('JUST_MY_FRIENDS') %></label>
                                            <label><%= $this->form->radio('post_add_comment_permission','me','class="radio"') %><%= xg_html('JUST_ME') %></label>
                                        </p>
                                    </fieldset>
                                    <fieldset>
                                     <div class="legend"><%= xg_html('COMMENT_MODERATION') %></div>
                                     <p><%= $this->commentsAreModerated ? xg_html('YOU_ARE_MODERATING_BLOG_COMMENTS') : xg_html('YOU_ARE_NOT_MODERATING_BLOG_COMMENTS') %>
                                     (<a href="<%= xnhtmlentities($this->_buildUrl('profile','settings')) %>" target="_blank"><%= xg_html('CHANGE') %></a>)</p>
                                    </fieldset>
                                </fieldset>
                                <input type="hidden" id="post_action" name="post_action" value=""/>
                                <input type="hidden" name="post_istext" value=""/>
                                <p class="buttongroup">
                                <?php if ($this->showDraftButton || $this->showDeleteButton) { ?>
                                    <span class="left">
                                    <?php if ($this->showDraftButton) { ?>
										<input type="submit" class="button" name="post_action_draft" id="post_draft" value="<%= qh(xg_html('SAVE_AS_DRAFT')) %>" />
                                    <?php } ?>
                                    <?php if ($this->showDeleteButton) { ?>
										<input type="button" class="button" name="post_action_delete" id="post_delete" value="<%= qh(xg_html('DELETE')) %>" />
                                    <?php } ?>
                                    </span>
                                <?php } ?>
                                    <span class="right">
										<input type="submit" class="button" name="post_action_preview" id="post_preview" value="<%= qh(xg_text('PREVIEW')) %>" />
										<input type="submit" class="button button-primary" name="post_action_publish" id="post_publish" value="<%= qh(xg_text('PUBLISH_POST')) %>" />
                                    </span>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="xg_1col">
            <div class="xg_1col first-child"><?php xg_sidebar($this); ?></div>
        </div>
    </div>
</div>
<?php xg_footer(); ?>