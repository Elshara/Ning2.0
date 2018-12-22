<?php
// This page is designed to function acceptably with Javascript turned off. [Jon Aquino 2007-01-24]
xg_header(W_Cache::current('W_Widget')->dir, $this->title); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div id="form_section" class="xg_3col first-child">
            <%= $this->renderPartial('fragment_navigation', '_shared', array('hideStartDiscussionLink' => !$this->editingExistingTopic )) %>
			<%= xg_headline($this->title)%>
            <%= XG_GroupHelper::groupLink() %>
            <div class="xg_colgroup">
                <div class="xg_3col first-child">
                    <div class="xg_module" style="z-index:2">
                        <div class="xg_module_head notitle"></div>
                        <div class="xg_module_body">
                            <form id="add_topic_form" action="<%= xnhtmlentities($this->formUrl) %>" method="post" enctype="multipart/form-data">
                                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                <dl class="errordesc msg" id="add_topic_form_notify" <%= $this->errors ? '' : 'style="display: none"' %>>
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
                                        <dt><label for="title"><%= xg_html('DISCUSSION_TITLE') %></label></dt>
                                        <dd><%= $this->form->text('title','id="title" style="width:98%" class="textfield" size="54" maxlength="' . Topic::MAX_TITLE_LENGTH . '"') %></dd>
                                    </dl>
                                    <dl>
                                        <dt><label for="post"><%= xg_html('FIRST_POST') %></label></dt>
                                        <dd><div class="texteditor"><%= $this->form->textarea('description','id="post" _maxlength="' . Topic::MAX_DESCRIPTION_LENGTH . '" rows="8" cols="60" dojoType="SimpleToolbar" _supressFileUpload="true"') %></div></dd>
                                    </dl>
                                    <?php
                                    if (count($this->categories) > 1) {
                                        $categoryIdsToTitles = array();
                                        foreach ($this->categories as $category) {
                                            $categoryIdsToTitles[$category->id] = $category->title;
                                        } ?>
                                        <dl>
                                            <dt><label for="category"><%= xg_html('CATEGORY') %></label></dt>
                                            <dd><%= $this->form->select('categoryId', $categoryIdsToTitles, false, 'id="category" style="max-width:550px;width:expression(550);"') %></dd>
                                        </dl>
                                    <?php
                                    } elseif (count($this->categories == 1)) { ?>
                                        <input type="hidden" name="categoryId" id="category" value="<%= $this->categories[0]->id %>" />
                                    <?php
                                    } ?>
                                    <dl>
                                        <dt><label for="tags"><%= xg_html('TAGS') %></label></dt>
                                        <dd><%= $this->form->text('tags','id="tags" class="textfield" size="51" style="width: 95%" maxlength="' . XG_TagHelper::MAX_TAGS_LENGTH . '"') %>
                                            <?php XG_App::ningLoaderRequire('xg.shared.ContextHelpToggler'); ?>
                                            <span class="context_help"><a dojoType="ContextHelpToggler" href="#"><img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/icon/help.gif')) %>" alt="?" title="<%= xg_html('WHAT_IS_THIS') %>" /></a>
                                                <span class="context_help_popup" style="display:none">
                                                    <span class="context_help_content">
                                                        <%= xg_html('TAGS_ARE_SHORT_DESCRIPTIONS_DISCUSSION') %>
                                                        <small><a dojoType="ContextHelpToggler" href="#"><%= xg_html('CLOSE') %></a></small>
                                                    </span>
                                                </span>
                                            </span>
                                        </dd>
                                    </dl>
                                    <?php
                                    if ($this->emptyAttachmentSlotCount) { ?>
                                        <dl>
                                            <dt><span><%= xg_html('UPLOAD_FILES') %></span></dt>
                                            <dd>
                                                <ul class="options">
                                                    <?php
                                                    for ($i = 1; $i <= $this->emptyAttachmentSlotCount; $i++) { ?>
                                                        <li <%= $this->errors["file$i"] ? 'class="error"' : '' %>><%= $this->form->file("file$i",'class="file"') %></li>
                                                    <?php
                                                    } ?>
                                                </ul>
                                            </dd>
                                        </dl>
                                    <?php
                                    } ?>
                                    <p class="buttongroup">
                                        <input type="submit" class="button button-primary" value="<%= xnhtmlentities($this->buttonText) %>">
										<input class="button" type="button" value="<%= xg_html('CANCEL')%>" onclick="window.location='<%=qh($this->cancelUrl)%>'">
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
                <div class="xg_module_body">
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
<?php XG_App::ningLoaderRequire('xg.forum.topic.newOrEdit', 'xg.shared.SimpleToolbar'); ?>
<?php xg_footer(); ?>
