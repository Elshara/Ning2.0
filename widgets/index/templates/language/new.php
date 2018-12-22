<?php
// Note that this page is fully functional without JavaScript. This is important, because the user
// may enter a syntax error into one of the JavaScript functions.  [Jon Aquino 2007-08-08]
XG_App::ningLoaderRequire('xg.index.language.new');
xg_header('manage', $title = xg_text('CREATE_NEW_TRANSLATION')); ?>

<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline($title)%>
            <div class="easyclear"><ul class="backlink navigation"><li><a href="<%= xnhtmlentities($this->_widget->buildUrl('language', 'list')) %>"><%= xg_html('BACK_TO_LANGUAGE_EDITOR') %></a></li></ul></div>
            <form id="translation_form" action="<%= xnhtmlentities($this->_buildUrl('language', 'create')) %>" method="POST">
                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                <%= $this->form->hidden('target') %>
                <div class="xg_module">
                    <div class="xg_module_body">
                        <p><%= xg_html('START_FROM_SCRATCH_CREATE') %></p>
                        <?php
                        if ($this->error) { ?>
                            <dl class="errordesc msg">
                                <dt><%= xg_html('THERE_HAS_BEEN_AN_ERROR') %></dt>
                                <dd><ol><li><%= xnhtmlentities($this->error) %></li></ol></dd>
                            </dl>
                        <?php
                        } ?>
                        <fieldset class="nolegend">
                            <dl>
                                <dt><strong><label for="language_name"><%= xg_html('LANGUAGE_NAME') %></label></strong></dt>
                                <dd>
                                    <%= $this->form->text('name','id="language_name" class="textfield" maxlength="' . Index_LanguageHelper::MAX_LANGUAGE_NAME_LENGTH . '"') %>
                                    <br />
                                    <small><%= xg_html('EXAMPLES_SWEDISH_FINLAND') %></small>
                                </dd>
                                <dt><strong><label for="language_base"><%= xg_html('BASED_ON') %></label></strong></dt>
                                <dd><%= $this->form->select('baseLocale', XG_LanguageHelper::nonCustomLocalesAndNames(), false, 'id="language_BASE"') %></dd>
                            </dl>
                        </fieldset>
                    </div>
                </div>
                <p class="align-right"><input type="submit" class="button" value="<%= xg_text('CREATE') %>" title="<%= xg_html('SAVE_ITEMS_ON_PAGE') %>"></p>
            </form>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this) ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
