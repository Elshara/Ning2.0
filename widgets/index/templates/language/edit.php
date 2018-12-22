<?php
// Note that this page is fully functional without JavaScript. This is important, because the user
// may enter a syntax error into one of the JavaScript functions.  [Jon Aquino 2007-08-08]
XG_App::ningLoaderRequire('xg.index.language.edit');
xg_header('manage', $title = xg_text('LANGUAGE_EDITOR_X', $this->localeName)); ?>

<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline($title)%>
            <div class="easyclear"><ul class="backlink navigation"><li><a href="<%= xnhtmlentities($this->_widget->buildUrl('language', 'list')) %>"><%= xg_html('BACK_TO_LANGUAGE_EDITOR') %></a></li></ul></div>
            <?php
            if ($this->displaySavedNotification) { ?>
                <form action="<%= xnhtmlentities($this->_buildUrl('language', 'setCurrentLocale', array('locale' => $this->locale, 'q' => $this->searchTerms, 'filter' => $this->filter, 'page' => $this->page))) %>" method="POST">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <div class="xg_module">
                        <div class="xg_module_body success">
                            <?php
                            if (! XN_Profile::current()->isOwner() || $this->locale == XG_LOCALE || $this->percentComplete < $this->percentCompleteThreshold) { ?>
                                <p class="last-child"><%= xg_html('TRANSLATION_CHANGES_SAVED', $this->percentComplete) %></p>
                            <?php
                            } else { ?>
                                <p><%= xg_html('TRANSLATION_CHANGES_SAVED_MAKE_CURRENT', $this->percentComplete) %></p>
                                <p class="buttongroup"><input type="submit" class="button" value="<%= xg_html('USE_THIS_TRANSLATION') %>"></p>
                            <?php
                            } ?>
                        </div>
                    </div>
                </form>
            <?php
            } elseif ($this->displayUploadedNotification) { ?>
                <form action="<%= xnhtmlentities($this->_buildUrl('language', 'setCurrentLocale', array('locale' => $this->locale, 'q' => $this->searchTerms, 'filter' => $this->filter, 'page' => $this->page))) %>" method="POST">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <div class="xg_module">
                        <div class="xg_module_body success">
                            <?php
                            if (! XN_Profile::current()->isOwner() || $this->locale == XG_LOCALE || $this->percentComplete < $this->percentCompleteThreshold) { ?>
                                <p class="last-child"><%= xg_html('TRANSLATION_UPLOAD_SUCCESSFUL', $this->percentComplete) %></p>
                            <?php
                            } else { ?>
                                <p><%= xg_html('TRANSLATION_UPLOAD_SUCCESSFUL_MAKE_CURRENT', $this->percentComplete) %></p>
                                <p class="buttongroup"><input type="submit" class="button" value="<%= xg_html('USE_THIS_TRANSLATION') %>"></p>
                            <?php
                            } ?>
                        </div>
                    </div>
                </form>
            <?php
            } elseif ($this->displayLocaleChangedNotification) { ?>
                <div class="xg_module">
                    <div class="xg_module_body success">
                        <p class="last-child"><%= xg_html('X_IS_CURRENT_LANGUAGE', $this->localeName) %></p>
                    </div>
                </div>
            <?php
            } elseif ($this->displayLocaleDeletedNotification) { ?>
                <div class="xg_module">
                    <div class="xg_module_body success">
                        <p class="last-child"><%= xg_html('TEXT_RESET_TO_ORIGINAL_VERSION', $this->localeName) %></p>
                    </div>
                </div>
            <?php
            } ?>
            <div class="xg_module">
                <div class="xg_module_body pad">
                    <?php
                    ob_start();
                    XG_PaginationHelper::outputPagination($this->totalMessageCount, $this->pageSize, 'top');
                    $paginationHtml = trim(ob_get_contents());
                    ob_end_clean();
                    if ($this->messages) {
                        ob_start(); ?>
                        <?php /* A rare case of inline JavaScript here, as any syntax errors in the I18N JavaScript will break all JavaScript rendered by the ning.loader, whereas we want the Save button to always work.  [Jon Aquino 2007-08-10] */ ?>
                        <input onclick="javascript:if (window.submitTranslationForm) { window.submitTranslationForm(); } else { document.getElementById('translation_form').submit(); }" type="button" class="button" value="<%= xg_text('SAVE_PAGE') %>">
                        <?php
                        $saveButtonHtml = ob_get_contents();
                        ob_end_clean();
                        echo !$paginationHtml ? '<p>' . $saveButtonHtml . '</p>' : str_replace('</ul>', '<li class="right">' . $saveButtonHtml . '</li></ul>', $paginationHtml);
                    } ?>
                    <dl id="translation_error_list" class="errordesc msg clear"<%= $this->errors ? '' : ' style="display: none"' %>>
                        <dt><%= xg_html('THERE_HAS_BEEN_AN_ERROR') %></dt>
                        <dd>
                            <ol>
                                <?php
                                foreach ($this->errors as $name => $errorMessage) { ?>
                                    <li><%= xnhtmlentities($errorMessage) %></li>
                                <?php
                                } ?>
                            </ol>
                        </dd>
                    </dl>
                    <table class="translator clear">
                        <thead>
                            <tr>
                            <th scope="col" width="50%"><%= xg_html('ORIGINAL_TEXT_X', XG_LanguageHelper::name(XG_LanguageHelper::isCustomLocale($this->locale) ? XG_LanguageHelper::baseLocale($this->locale) : 'en_US')) %></th>
                                <th scope="col" width="50%">
                                    <span class="left"><%= xg_html('CUSTOM_TEXT') %></span>
                                    <?php
                                    if ($this->displayRestoreDefaultsButton) { ?>
                                        <form id="restore_defaults_form" class="right" action="<%= xnhtmlentities($this->_buildUrl('language', 'delete', array('locale' => $this->locale, 'target' => $this->_buildUrl('language', 'edit', array('locale' => $this->locale, 'q' => $this->searchTerms, 'filter' => $this->filter, 'page' => $this->page))))) %>" method="POST">
                                            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                            <input type="submit" class="button" value="<%= xg_html('RESET_TEXT_TO_ORIGINAL_VERSION') %>" />
                                        </form>
                                    <?php
                                    } ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="filter">
                                <td>
                                    <form action="<%= xnhtmlentities($this->_buildUrl('language', 'edit')) %>">
                                        <p>
                                            <input type="hidden" name="locale" value="<%= xnhtmlentities($this->locale) %>" />
                                            <input name="q" type="text" class="textfield" size="25" value="<%= xnhtmlentities($_GET['q']) %>" />
                                            <input type="submit" class="button" value="<%= xg_html('SEARCH') %>" />
                                            <br />
                                            <small><%= xg_html('EXAMPLES_TABS_MEMBERS_FRIENDS') %></small>
                                        </p>
                                    </form>
                                </td>
                                <td>
                                    <form action="<%= xnhtmlentities($this->_buildUrl('language', 'edit')) %>">
                                        <p>
                                            <select name="filter">
                                                <?php
                                                foreach (array('all' => xg_text('SHOW_ALL_TEXT'), 'missing' => xg_text('SHOW_MISSING_TEXT'), 'changed' => xg_text('SHOW_CHANGED_TEXT')) as $filter => $filterName) { ?>
                                                    <option value="<%= xnhtmlentities($filter) %>"<%= $filter == $this->filter ? ' selected="selected"' : '' %>><%= xnhtmlentities($filterName) %></option>
                                                <?php
                                                } ?>
                                            </select>
                                            <input type="hidden" name="locale" value="<%= xnhtmlentities($this->locale) %>" />
                                            <input type="submit" class="button" value="<%= xg_html('FILTER') %>" />
                                            <br />
                                            <small><%= xg_html('KEY_MISSING_TEXT', 'class="missing"', 'class="changed"') %></small>
                                        </p>
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php
                    if ($this->messages) { ?>
                        <form id="translation_form" action="<%= xnhtmlentities($this->_buildUrl('language', 'update', array('locale' => $this->locale, 'q' => $this->searchTerms, 'filter' => $this->filter, 'page' => $this->page))) %>" method="POST">
                            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                            <table class="translator clear">
                                <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($this->messages as $message) {
                                        $alt = $i % 2 == 1;
                                        $classes = array();
                                        /** "missing" takes precedence over "changed" (BAZ-4274)  [Jon Aquino 2007-08-31] */
                                        if ($message['missing']) { $classes[] = 'missing'; }
                                        elseif ($message['changed'] && ! XG_LanguageHelper::isCustomLocale($this->locale)) { $classes[] = 'changed'; }
                                        if ($message['errorMessage']) { $classes[] = 'error'; } ?>
                                        <tr<%= $message['note'] || $message['errorMessage'] ? '' : ' style="display:none"' %><%= $alt ? ' class="alt"' : '' %>>
                                            <td colspan="2">
                                                <%= $message['note'] ? '<p>' . $message['note'] . '</p>' : '' %>
                                                <%= $message['errorMessage'] ? '<p class="errordesc">' . xnhtmlentities($message['errorMessage']) . '</p>' : '' %>
                                            </td>
                                        </tr>
                                        <tr<%= $alt ? ' class="alt"' : '' %>>
                                            <td width="50%">
                                                <?php
                                                if ($message['wrap'] === false) { ?>
                                                    <textarea wrap="off" rows="<%= $message['rows'] %>" readonly="readonly"><%= xnhtmlentities($message['sourceText']) %></textarea>
                                                <?php
                                                } else { ?>
                                                    <%= xnhtmlentities($message['sourceText']) %>
                                                <?php
                                                } ?>
                                            </td>
                                            <td width="50%"<%= $classes ? ' class="' . implode(' ', $classes) . '"' : '' %>>
                                                <textarea name="messages[<%= xnhtmlentities($message['name']) %>]" <%= $message['wrap'] === false ? 'wrap="off"' : '' %> rows="<%= $message['rows'] %>"><%= xnhtmlentities($message['targetText']) %></textarea>
                                                <?php
                                                if ($message['isTabText']) { ?>
                                                    <p class="note"><%= xg_html('TEXT_FOR_HEADER_TAB') %></p>
                                                <?php
                                                } ?>
                                                <input type="hidden" name="messageNames[]" value="<%= xnhtmlentities($message['name']) %>" />
                                            </td>
                                        </tr>
                                        <?php
                                        $i++;
                                    } ?>
                                </tbody>
                            </table>
                            <?php
                            ob_start();
                            XG_PaginationHelper::outputPagination($this->totalMessageCount, $this->pageSize);
                            $paginationHtml = trim(ob_get_contents());
                            ob_end_clean();
                            ob_start(); ?>
                            <input type="submit" class="button" value="<%= xg_text('SAVE_PAGE') %>">
                            <?php
                            $saveButtonHtml = ob_get_contents();
                            ob_end_clean();
                            echo !$paginationHtml ? '<p>' . $saveButtonHtml . '</p>' : str_replace('</ul>', '<li class="right">' . $saveButtonHtml . '</li></ul>', $paginationHtml); ?>
                        </form>
                    <?php
                    } elseif ($this->filter == 'missing') { ?>
                        <p><%= xg_html('NO_MISSING_ITEMS', 'href="' . xnhtmlentities($this->_buildUrl('language', 'edit', array('locale' => $this->locale))) . '"') %></p>
                    <?php
                    } elseif ($this->filter == 'changed') { ?>
                        <p><%= xg_html('NO_CHANGED_ITEMS', 'href="' . xnhtmlentities($this->_buildUrl('language', 'edit', array('locale' => $this->locale))) . '"') %></p>
                    <?php
                    } elseif ($this->searchTerms) { ?>
                        <p><%= xg_html('COULD_NOT_FIND_ITEMS_MATCHING', xnhtmlentities($this->searchTerms), 'href="' . xnhtmlentities($this->_buildUrl('language', 'edit', array('locale' => $this->locale))) . '"') %></p>
                    <?php
                    } ?>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this) ?>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
