<?php
// Note that this page is fully functional without JavaScript. This is important, because the user
xg_header('manage', $title = xg_text('ADVANCED_IMPORT')); ?>

<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline($title)%>
            <div class="easyclear"><ul class="backlink navigation"><li><a href="<%= xnhtmlentities($this->_widget->buildUrl('language', 'list')) %>"><%= xg_html('BACK_TO_LANGUAGE_EDITOR') %></a></li></ul></div>
            <div class="xg_module">
                <div class="xg_module_body pad">
                    <?php
                    if ($this->errors) { ?>
                        <dl class="errordesc msg" id="add_topic_form_notify" <%= $this->errors ? '' : 'style="display: none"' %>>
                            <dt><%= xg_html('THERE_HAS_BEEN_AN_ERROR') %></dt>
                            <dd>
                                <ol>
                                    <?php
                                    foreach ($this->errors as $error) { ?>
                                        <li><%= xnhtmlentities($error) %></li>
                                    <?php
                                    } ?>
                                </ol>
                            </dd>
                        </dl>
                    <?php
                    } ?>

                    <p><%= xg_html('IF_YOU_CREATED_TRANSLATION', 'href="' . xnhtmlentities($this->_buildUrl('language', 'new', array('target' => 'upload'))) . '"') %></p>
                    <p><%= xg_html('TRANSLATION_FILE_HAS_FILENAME_LIKE', 'href="http://networkcreators.ning.com/profiles/blog/show?id=492224:BlogPost:60168"') %></p>
                    <form action="<%= xnhtmlentities($this->_buildUrl('language', 'doUpload')) %>" method="POST" enctype="multipart/form-data">
                        <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                        <p>
                            <label for="language"><%= xg_html('ADD_TO_LANGUAGE') %></label>
                            <%= $this->form->select('locale', XG_LanguageHelper::localesAndNames(), false, 'id="language"') %>
                        </p>
                        <p>
                            <label for="translation_file"><%= xg_html('SELECT_TRANSLATION_FILE') %></label>
                            <%= $this->form->file("file1",'class="file" id="translation_file"') %>
                        </p>
                        <p class="buttongroup"><input type="submit" class="button" value="<%= xg_html('UPLOAD_LANGUAGE_FILE') %>" /></p>
                    </form>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this) ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
