<?php xg_header('manage',xg_text('FLICKR_IMPORT_SETUP')); ?>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
                <div class="xg_colgroup">
                    <div class="xg_3col first-child">
						<%= xg_headline(xg_text('FLICKR_IMPORT_SETUP'))%>
						<%= $this->renderPartial('fragment_success', 'admin'); %>
                        <div class="xg_module instructions">
                            <div class="xg_module_body pad">
                                <?php
                                if ($this->error) {
                                    echo '<p class="errordesc" id="xg_flickr_form_notify">' . $this->error ."</p>\n";
                                }
                                ?>
                                <p><%= xg_html('FLICKR_INTRO', 'href="#flickrkey"')%></p>
                                <p><strong><%= xg_html('FLICKR_GET_ACCOUNT', 'href="http://www.flickr.com" target="_blank"')%></strong></p>
                            </div>
                            <div class="xg_module_body pad">
                                <img class="right" style="margin-bottom:1em" src="<%= xnhtmlentities(xg_localized_url('FLICKR_SCREENSHOT_GETKEY')) %>" alt="<%= xg_text('FLICKR_STEP1_APPLY_SCREEN') %>" />
                                <h3><%= xg_html('FLICKR_STEP1_TITLE') %></h3>
                                <p><%= xg_html('FLICKR_STEP1_INTRO', 'href="http://www.flickr.com/services/api/keys/apply/" target="_blank"')%></p>
                                <ol>
                                    <li>
                                        <p><%= xg_html('FLICKR_STEP1_A')%></p>
                                    </li>
                                    <li>
                                        <p><%= xg_html('FLICKR_STEP1_B')%></p>
                                    </li>
                                    <li>
                                        <p><%= xg_html('FLICKR_STEP1_C')%></p>
                                    </li>
                                    <li>
                                        <p><%= xg_html('FLICKR_STEP1_D')%></p>
                                    </li>
                                </ol>
                                <p><%= xg_html('FLICKR_STEP1_APPLY')%></p>
                                <br class="clear" />
                                <img class="right" src="<%= xnhtmlentities(xg_localized_url('FLICKR_SCREENSHOT_KEY')) %>" alt="<%= xg_text('FLICKR_STEP1_API_SCREEN') %>" />
                                <ol start="5">
                                    <li>
                                        <p><%= xg_html('FLICKR_STEP1_E')%></p>
                                    </li>
                                </ol>
                            </div>
                            <div class="xg_module_body pad">
                                <img class="right" src="<%= xnhtmlentities(xg_localized_url('FLICKR_SCREENSHOT_SETUPKEY')) %>" alt="<%= xg_text('FLICKR_STEP2_SETUP_SCREEN') %>" />
                                <h3><%= xg_html('FLICKR_STEP2_TITLE') %></h3>
                                <ol start="6">
                                    <li>
                                        <p>
                                            <%= xg_html('FLICKR_STEP2_F') %><br />
                                            <input type="text" value="<%= $this->flickrCallback %>" class="textfield"/>
                                        </p>
                                    </li>
                                </ol>
                                <p><%= xg_html('FLICKR_STEP2_SAVE_CHANGES')%></p>
                            </div>
                            <div class="xg_module_body pad">
                                <img class="right margin-bottom" src="<%= xnhtmlentities(xg_localized_url('FLICKR_SCREENSHOT_KEYINFO')) %>" alt="<%= xg_text('FLICKR_STEP3_KEYS_SCREEN') %>" />
                                <h3><%= xg_html('FLICKR_STEP3_TITLE') %></h3>
                                <p><%= xg_html('FLICKR_STEP3_CONGRATULATIONS') %></p>
                                <p><strong><%= xg_html('FLICKR_STEP3_COMPLETE', $this->appName) %></strong></p>
                                <form id="flickrkey" method="post" action="<%= xnhtmlentities($this->_buildUrl('flickr','save')) %>">
                                        <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                    <fieldset class="nolegend">
                                        <p>
                                            <label for="flickr_api_key"><%= xg_html('FLICKR_API_KEY') %></label><br />
                                            <%= $this->form->text('flickr_api_key','id="flickr_api_key" class="textfield" size="35"') %>
                                        </p>
                                        <p>
                                            <label for="flickr_secret"><%= xg_html('FLICKR_SECRET') %></label><br />
                                            <%= $this->form->text('flickr_secret','id="flickr_secret" class="textfield" size="35"') %>
                                        </p>
                                    </fieldset>
                                </form>
                                <p class="buttongroup">
                                        <input type="button" id="flickr_form_submit" onclick="document.forms.flickrkey.submit();"
                                        	class="button button-primary" value="<%= qh(xg_html('SAVE')) %>" />
                                        <input type="button" id="flickr_remove_submit" onclick="var f=document.forms.flickrkey;f.action='<%= xnhtmlentities($this->_buildUrl('flickr', 'deactivate')) %>';f.submit();"
											class="button button-primary" value="<%= qh(xg_html('REMOVE')) %>" />
										<input type="button" onclick="location.href='<%= xnhtmlentities($this->_buildUrl('admin' ,'manage')) %>';"
											class="button button-primary" value="<%= qh(xg_html('CANCEL')) %>" />
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php xg_footer(); ?>
