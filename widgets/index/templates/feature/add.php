<?php xg_header('manage', xg_text('FEATURES'), null, array('forceDojo'=>true)); ?>
<?php XG_App::ningLoaderRequire('xg.index.feature.add'); ?>
<script type="text/javascript">
    xg.addOnRequire(function() {
        xg_handleLaunchBarSubmit = xg.index.feature.add.handleLaunchBarSubmit;
    });
</script>
    <div id="xg_body">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
                <?php
                if (!XG_App::appIsLaunched()) { ?>
                    <div id="xg_setup_next_header_top">
                        <?php W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_SetupHelper.php'); ?>
                        <%= Index_SetupHelper::nextButton($this->nextLink, true); %>
                    </div>
                <?php
                } ?>
				<%= xg_headline(xg_text('ADD_FEATURES')) %>
				<?php $this->renderPartial('fragment_success', 'admin'); ?>
                <form id="xg_add_features_form" name="xg_add_features_form" method="post" action="<?php echo $this->_widget->buildUrl('feature', 'add') ?>">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <input type="hidden" name="successTarget"/>
                    <input type="hidden" name="xg_feature_layout"/>
                    <input type="hidden" name="successfulDrop">

                    <div class="xg_colgroup">
                        <div class="xg_3col first-child">

                            <div class="xg_module">
                                <div class="xg_module_body pad">
                                    <?php if (!XG_App::appIsLaunched()) { ?>
                                        <p><big><%= xg_html('ORGANIZE_FEATURES') %></big></p>
                                    <?php } else if ($this->showPremiumServicesPromo) { ?>
                                        <p><%= xg_html('CHOOSE_FEATURES_PREMIUM_SERVICES', 'href="' . xnhtmlentities($this->premiumServicesUrl) . '"') %></p>
                                    <?php } else { ?>
                                        <p><%= xg_html('CHOOSE_FEATURES'); %></p>
                                    <?php } ?>
                                    <div class="left">
                                        <h4><%= xg_html('FEATURES') %></h4>
                                        <div>
                                            <ul class="feature-list" id="xg_add_features_source" style="display:none" _initialVisibleSourceFeatureCount="<%= $this->initialVisibleSourceFeatureCount %>">
                                                <?php
                                                    XG_App::ningLoaderRequire('xg.shared.ContextHelpToggler');
                                                    foreach ($this->embeds as $name => $embed) {
                                                    //TODO If userbox is ever going to be movable/removable we will need to implement the different
                                                    // width/height/class here as we have below.

                                                    // We use _dojotype because we don't want nested widgets to be parsed. [Andrey 2008-05-23]
                                                    ?>
                                                    <li class="feature movable" xg_embed_key="<%= $name %>"
                                                            xg_embed_limit="<%= $embed['embedLimit'] %>" xg_width_option="<%= $embed['widthOption'] %>">
                                                        <img src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/features/' . $embed['iconName']))) %>"
                                                            width="16" height="16"
                                                            alt="<%= xnhtmlentities($embed['title']) %>" />
                                                        <%= xnhtmlentities($embed['title']) %>
                                                        <span class="context_help"><a _dojotype="ContextHelpToggler" href="#"><img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/icon/help.gif')) %>" alt="?" title="<%= xg_html('WHAT_IS_THIS') %>" /></a>
                                                            <span class="context_help_popup" style="display:none">
                                                                <span class="context_help_content">
                                                                    <%= xnhtmlentities($embed['about']) %>
                                                                    <small><a _dojotype="ContextHelpToggler" href="#"><%= xg_html('CLOSE') %></a></small>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                        <p><small><a id="view_all_features_link" style="display:none" href="#"><%= xg_html('VIEW_ALL_FEATURES') %></a></small></p>
                                        <div id="xg_add_features_trash">
                                            <p><%= xg_html('DRAG_HERE_TO_REMOVE_FEATURE') %></p>
                                        </div>
                                    </div>
                                    <div class="right margin-bottom">
                                        <h4><%= xg_html('LAYOUT') %></h4>
                                        <div id="homelayout" class="easyclear">
                                            <div class="header">
                                                <span class="networkname"><%= xnhtmlentities(XN_Application::load()->name) %></span>
                                                <div id="xg_add_features_allpagesnote" class="callout allpagesnote"><span><%= xg_html('FEATURES_FOR_ALL_PAGES') %></span></div>
                                                <?php if ($this->showPremiumServicesPromo) { ?>
                                                    <div id="xg_add_features_premiumservicesnote" class="callout"><span><%= xg_html('TO_CONTROL_PREMIUM_SERVICES_LINK', 'href="' . xnhtmlentities($this->premiumServicesUrl) . '"') %></span></div>
                                                <?php } ?>
                                            </div>
                                            <div class="body">
                                            <?php for ($col = 1; $col <= 3; $col++) { ?>
                                                <div class="column col<%= $col %>">
                                                <?php if ($col === 3) { ?>
                                                    <ul id="xg_layout_column_sidebar" class="feature-list">
                                                        <?php
                                                        foreach ($this->initialEmbeds['sidebar'] as $initial) {
                                                            $embed = $this->embeds[$initial['embedKey']];
                                                            $userbox = ($embed['title'] === xg_text('USERNAME'));
                                                            $class = ($userbox ? ' userbox' : '');
                                                        ?>
                                                            <li class="feature movable<%= ($embed['fixed'] ? ' noedit' : '') %><%= $class %>" xg_embed_key="<%= $initial['embedKey'] %>"
                                                                xg_embed_limit="<%= $embed['embedLimit'] %>" xg_width_option="<%= $embed['widthOption'] %>"
                                                                xg_embed_instance_id="<%= $initial['embedInstanceId'] %>">
                                                            <?php if ($userbox) { ?>
                                                                <%= xnhtmlentities($embed['title']) %>
                                                                <img src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/features/' . $embed['iconName']))) %>"
                                                                    alt="<%= xnhtmlentities($embed['title']) %>" />
                                                            <?php } else { ?>
                                                                <img src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/features/' . $embed['iconName']))) %>" width="16" height="16"
                                                                    alt="<%= xnhtmlentities($embed['title']) %>" />
                                                                <%= xnhtmlentities($embed['title']) %>
                                                            <?php } ?>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                <?php } ?>
                                                <ul class="feature-list col<%= $col %>" id="xg_layout_column_<%= $col %>">
                                                <?php foreach ($this->initialEmbeds[$col] as $initial) {
                                                    $embed = $this->embeds[$initial['embedKey']];
                                                ?>
                                                    <li class="feature movable" xg_embed_key="<%= $initial['embedKey'] %>"
                                                        xg_embed_limit="<%= $embed['embedLimit'] %>" xg_width_option="<%= $embed['widthOption'] %>"
                                                        xg_embed_instance_id="<%= $initial['embedInstanceId'] %>">
                                                    <img src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/features/' . $embed['iconName']))) %>" width="16" height="16"
                                                        alt="<%= xnhtmlentities($embed['title']) %>" />
                                                    <%= xnhtmlentities($embed['title']) %>
                                                    </li>
                                                <?php } ?>
                                                </ul>
                                                </div>
                                            <?php } ?>
                                            </div>
                                        </div>

                                    </div>
                                    <?php if ($this->displayPrelaunchButtons) {
                                        $this->renderPartial('_backnext', 'embed');
                                    } else { ?>
                                        <p class="buttongroup">
											<input type="button" class="button button-primary" onClick="xg.index.feature.add.submitForm()" value="<%= qh(xg_html('SAVE')) %>" />
                                        	<a class="button" href="<%= $this->_widget->buildUrl('admin', 'manage') %>"><%= xg_html('CANCEL')%></a>
										</p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <%= $this->hiddenLayoutDetails %>
        </div>

    </div>
<?php xg_footer(); ?>
