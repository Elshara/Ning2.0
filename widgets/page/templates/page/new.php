<?php
xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('ADD_A_PAGE')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div id="form_section" class="xg_3col first-child">
            <%= $this->renderPartial('fragment_navigation', '_shared', array('noAddLink' => true)) %>
			<%= xg_headline($title)%>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module">
                        <div class="xg_module_head notitle"></div>
                        <div class="xg_module_body">
                            <form id="add_page_form" action="<%= xnhtmlentities($this->_buildUrl('page', 'create')) %>" method="post">
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
                                        <dd><%= $this->form->text('title','id="title" style="width: 548px;" class="textfield" size="54" maxlength="' . Page::MAX_TITLE_LENGTH . '"') %></dd>
                                    </dl>
                                    <dl>
                                        <dt><label for="post"><%= xg_html('PAGE_CONTENT') %></label></dt>
                                        <dd><div class="texteditor"><%= $this->form->textarea('description','id="post" _maxlength="' . Page::MAX_DESCRIPTION_LENGTH . '" rows="20" cols="75" style="width: 548px;" dojoType="SimpleToolbar"') %></div></dd>
                                    </dl>
                                    <dl>
                                        <dt><label for="post"><%= xg_html('ALLOW_COMMENTS') %></label></dt>
                                        <dd><input type="checkbox" name="allowComments" class="checkbox" /></dd>
                                    </dl>
                                    <dl>
                                        <dt><label for="tags"><%= xg_html('TAGS') %></label></dt>
                                        <dd><%= $this->form->text('tags','id="tags" class="textfield" size="51" style="width: 530px;" maxlength="' . XG_TagHelper::MAX_TAGS_LENGTH . '"') %>
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
                                        <input type="submit" class="button" value="<%= xg_html('CREATE_PAGE') %>">
										<input class="button" type="button" value="<%= xg_html('CANCEL')%>" onclick="window.location='<%=qh($this->_buildUrl('page','list'))%>'">
                                    </p>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="spinner_section" class="xg_3col first-child" style="display:none">
            <h1><%= xg_html('UPLOADING_FILES') %></h1>
            <div class="xg_module">
                <div class="xg_module_body pad">
                    <h3><img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/spinner.gif') %>" alt="<%= xg_html('SPINNER') %>" class="left" style="margin-right:15px" /><strong><%= xg_html('KEEP_PAGE_OPEN_FORUM') %></strong></h3>
                    <p><%= xg_html('MEANWHILE_FEEL_FREE_FORUM', 'href="/" target="_blank"', xnhtmlentities(XN_Application::load()->name)) %></p>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php XG_App::ningLoaderRequire('xg.page.page.new', 'xg.shared.SimpleToolbar'); ?>
<?php xg_footer(); ?>
