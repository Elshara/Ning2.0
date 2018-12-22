<?php xg_header(W_Cache::current('W_Widget')->dir, $title = xg_text('ADD_A_VIDEO')); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<?php XG_PageHelper::subMenu(Video_HtmlHelper::subMenu($this->_widget, 'none')) ?>
			<%= xg_headline($title)%>
            <div class="xg_colgroup" id="add_video_module">
                <div class="xg_2col first-child">
                    <div class="xg_module">
                        <div class="xg_module_body pad">
                            <h3><%= xg_html('UPLOAD_A_VIDEO_FROM_YOUR_COMPUTER') %></h3>
                            <dl class="errordesc msg" id="add_video_form_notify" <%= $this->errors ? '' : 'style="display: none"' %>>
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
                            <?php if(!$this->hideBulkUploaderReferences) { ?>
                            <p style="margin:1em 0"><img class="left" style="margin-right:5px" src="<%= xnhtmlentities(xg_cdn($this->_widget->buildResourceUrl('gfx/add-multiple.png'))) %>" alt="" /><%= xg_html('HAVE_LOT_OF_VIDEOS') %><br />
                            <a href="<%= xnhtmlentities($this->_widget->buildUrl('video', 'newWithUploader')) %>"><strong><%= xg_html('TRY_BULK_VIDEO_UPLOADER') %></strong></a></p>
                            <?php } ?>
                            <form id="add_video_form" action="<%= xnhtmlentities($this->_buildUrl('video', 'create')) %>" method="post" enctype="multipart/form-data">
                                <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                                <fieldset class="nolegend">
                                    <p><big><input id="video_upload" name="file" type="file" class="file" /></big></p>
                                    <p><small><%= xg_html('I_HAVE_RIGHT_TO_UPLOAD_VIDEO', 'href="' . xnhtmlentities(W_Cache::getWidget('main')->buildUrl('authorization', 'termsOfService', array('previousUrl' => XG_HttpHelper::currentUrl()))) . '"') %></small></p>
                                    <p class="buttongroup"><input type="submit" class="button" value="<%= xg_html('ADD_VIDEO') %>" /></p>
                                </fieldset>
                            </form>
                        </div>
                        <div class="xg_module_body pad">
                            <h4><%= xg_html('HOW_IT_WORKS') %></h4>
                            <p><%= xg_html('FILES_MUST_BE_100MB') %></p>
                        </div>
                    </div>
                </div>
                <div class="xg_1col">
                    <div class="xg_module">
                        <div class="xg_module_body">
                            <h3><%= xg_html('MORE_WAYS_TO_ADD_VIDEOS') %></h3>
                            <?php $this->renderPartial('fragment_addByEmail', array('appName'=>$this->appName)); ?>
                        </div>
                        <div class="xg_module_body">
                            <?php $this->renderPartial('fragment_addEmbed'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id="adding_video_module" class="xg_3col first-child" style="display:none;">
                <div class="xg_module">
                    <div class="xg_module_body pad">
                        <h3><img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/spinner.gif') %>" alt="<%= xg_html('SPINNER') %>" class="left" style="margin:0 15px 60px 0" /><strong><%= Video_SecurityHelper::isApprovalRequired() ? xg_html('PERSON_IN_CHARGE', xnhtmlentities(XN_Application::load()->name)) : xg_html('LEAVE_WINDOW_OPEN') %></strong></h3>
                        <p><%= Video_SecurityHelper::isApprovalRequired() ? xg_html('KEEP_PAGE_OPEN') : xg_html('MEANWHILE_FEEL_FREE', 'href="/" target="_blank"', xnhtmlentities(XN_Application::load()->name)) %></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this); ?>
        </div>
    </div>
</div>
<?php XG_App::ningLoaderRequire('xg.video.index._shared', 'xg.video.video.new'); ?>
<?php xg_footer(); ?>
