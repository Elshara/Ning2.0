<?php xg_header('manage',xg_text('MANAGE')); ?>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
				<%= xg_headline(xg_text('ANALYTICS'))%>
				<?php $this->renderPartial('fragment_success'); ?>
                <div class="xg_colgroup">
                    <div class="xg_3col first-child">
                        <div class="xg_module">
                            <div class="xg_module_body pad">
                                <?php if($this->error) { ?>
                                    <p class="errordesc">
                                        <%= xg_html('INVALID_ANALYTICS_CODE') %>
                                    </p>
                                <?php }?>
                                <form id="trackingjs" name="trackingjs" action="" method="post">
                                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                    <fieldset class="nolegend">
                                        <p><%= xg_html('ANALYTICS_USE_THIS_BOX','href="http://www.google.com/analytics/"') %></p>
                                        <p><%= xg_html('ANALYTICS_CODE_YOU_ENTER')%></p>
                                        <p><textarea id="pageEnd" name="pageEnd" cols="55" rows="20" class="wide"><%= $this->pageEndCode %></textarea></p>
                                    </fieldset>
                                </form>
                                <p class="buttongroup">
                                    <input type="button" class="button button-primary" onclick="document.forms.trackingjs.submit();" value="<%= xg_html('SAVE') %>" />
                                    <a href="<%= $this->_widget->buildUrl('admin', 'manage') %>" class="button"><%= xg_html('CANCEL')%></a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="xg_1col last-child">
                <?php xg_sidebar($this) ?>
            </div>
        </div>
    </div>
<?php xg_footer(); ?>
