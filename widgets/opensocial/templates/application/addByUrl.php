<?php xg_header(W_Cache::current('W_Widget')->dir, $this->title); 
XG_App::ningLoaderRequire('xg.opensocial.application.addByUrl'); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
	    <div class="xg_module">
            <%= xg_headline(xg_html('ADD_APPLICATION_BY_URL'),
                            array('byline1Html' => xg_html('VIEW_ALL_APPLICATIONS', 'href="' .  qh($this->_buildUrl('application', 'list')) . '"' ))) %>
            <div class="xg_module_body">
                <?php $this->renderPartial('fragment_errorMsg', 'application'); ?>
                <form action="<%= xnhtmlentities($this->addAppUrl) %>" method="post" id="add_by_url_form">
                    <fieldset class="last-child">
                        <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                        <%= $this->form->hidden("installedByUrl") %>
                        <p><%= xg_html('TO_ADD_UNLISTED_APPLICATION') %></p>
                        <dl>
                            <dt><label><%= xg_html('APPLICATION_URL') %></label></dt>
                            <dd><%= $this->form->text("appUrl", 'class="textfield wide"') %></dd>
                            <dd><small><em><%= xg_html('OPENSOCIAL_EXAMPLE_URL') %></em></small></dd>
                        </dl>
                        <p class="buttongroup">
                            <input type="submit" value="<%= xg_html('ADD_APPLICATION') %>" class="button" id="addApplication"/>
                        </p>
                    </fieldset>
                </form>
            </div>
        </div><!--/xg_module-->
    </div>
    <div class="xg_column xg_span-4 xg_last">        
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
