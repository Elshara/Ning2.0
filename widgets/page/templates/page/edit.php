<?php
xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('EDIT_PAGE')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div id="form_section" class="xg_3col first-child">
            <%= $this->renderPartial('fragment_navigation', '_shared') %>
			<%= xg_headline($title)%>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <div class="xg_module_head notitle"></div>
                        <div class="xg_module_body">
                            <form id="add_page_form" action="<%= xnhtmlentities($this->_buildUrl('page', 'update', '?id=' . $this->page->id)) %>" method="post">
                                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                <dl class="errordesc msg" id="add_page_form_notify" <%= $this->errors ? '' : 'style="display: none"' %>>
                                    <?php
                                    if ($this->errors) { ?>
                                        <dt><%= xg_html('THERE_HAS_BEEN_AN_ERROR') %></dt>
                                        <dd>
                                            <ol>
                                                <?php
                                                foreach (array_unique($this->errors) as $error) { ?>
                                                    <li><%= xnhtmlentities($error) %></li>
                                                <?php
                                                } ?>
                                            </ol>
                                        </dd>
                                    <?php
                                    } ?>
                                </dl>
                                <fieldset class="nolegend">
                                    <dl>
                                        <dt><label for="title"><%= xg_html('PAGE_TITLE_COLON') %></label></dt>
                                        <dd><input type="text" id="title" name="title" style="width: 552px;" class="textfield large" size="54" maxlength="<%= Page::MAX_TITLE_LENGTH %>" value="<%= $this->page->title %>"/></dd>
                                    </dl>
                                    <dl>
                                        <dt><label for="post"><%= xg_html('PAGE_CONTENT') %></label></dt>
                                        <dd><div class="texteditor"><textarea id="post" style="width: 552px;" cols="75" rows="20" _maxlength="100000" name="description" dojoType="SImpleToolbar" /><%= $this->page->description %></textarea></div></dd>
                                    </dl>
                                    <dl>
                                        <dt><label for="post"><%= xg_html('ALLOW_COMMENTS') %></label></dt>
                                        <dd><input type="checkbox" name="allowComments" class="checkbox" <%= $this->page->my->allowComments == 'Y' ? 'checked="checked"' : '' %> /></dd>
                                    </dl>
                                    <dl>
                                        <dt><label for="tags"><%= xg_html('TAGS') %></label></dt>
                                        <dd><input id="tags" class="textfield large" type="text" maxlength="2000" style="width: 535px;" size="51" name="tags" value="<%= xnhtmlentities(XG_TagHelper::getTagStringForObjectAndUser($this->page, XN_Profile::current()->screenName)) %>"/>
                                            <?php XG_App::ningLoaderRequire('xg.shared.ContextHelpToggler'); ?>
                                            <span class="context_help"><a dojoType="ContextHelpToggler" href="#"><img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/icon/help.gif')) %>" alt="?" title="<%= xg_html('WHAT_IS_THIS') %>" /></a>
                                                <span class="context_help_popup" style="display:none">
                                                    <span class="context_help_content">
                                                        <%= xg_html('TAGS_ARE_SHORT_DESCRIPTIONS_PAGE') %>
                                                        <small><a dojoType="ContextHelpToggler" href="#"><%= xg_html('CLOSE') %></a></small>
                                                    </span>
                                                </span>
                                            </span>
                                        </dd>
                                    </dl>
                                    <p class="buttongroup">
                                        <input type="submit" class="button" value="<%= xg_html('UPDATE_PAGE') %>">
										<input class="button" type="button" value="<%= xg_html('CANCEL')%>" onclick="window.location='<%=qh($this->_buildUrl('page','list'))%>'">
                                    </p>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php XG_App::ningLoaderRequire('xg.shared.SimpleToolbar'); ?>
<?php xg_footer(); ?>
