<?php XG_App::ningLoaderRequire('xg.html.embed.embed', 'xg.shared.util'); ?>
<div class="xg_module html_module module_text xg_reset"
    <?php
    if ($this->userCanEdit) { ?>
        dojoType="HtmlModule" url="<%= xnhtmlentities($this->_buildUrl('embed', 'setValues', array('id' => $this->embed->getLocator(), 'xn_out' => 'json', 'maxEmbedWidth' => $this->maxEmbedWidth, 'sidebar' => XG_App::isSidebarRendering() ? '1' : '0'))) %>"
        <?php /* Only admins can add <script>-based widgets [Jon Aquino 2007-05-25] */ ?>
        updateUrl="<%= xnhtmlentities($this->_buildUrl('embed', 'updateEmbed', array('id' => $this->embed->getLocator(), 'xn_out' => 'json'))) %>"
        _title="<%= xnhtmlentities($this->title) %>"
        _maxLength="<%= $this->maxLength %>"
        addWidgetUrl="<%= XG_SecurityHelper::userIsAdmin() ? xnhtmlentities($this->_buildUrl('embed', 'widgets')) : '' %>"
        hasDefaultContent="<%= $this->hasDefaultContent ? 'true' : 'false' %>"
    <?php
    } ?>
    >
    <%= $this->renderPartial('fragment_moduleHead') %>
    <?php
    if ($this->displayHtml) { ?>
        <div class="xg_module_body">
            <%= $this->displayHtml %>
        </div>
    <?php } else { ?>
        <div class="xg_module_body" style="display:none;"></div>
        <div class="xg_module_foot">
            <ul><li class="left">
            <a href="#" class="desc add"><%= xg_html('ADD_TEXT') %></a>
            <?php
                if ($this->maxEmbedWidth > 480) { ?>
                  <%= xg_html('CLICK_EDIT_TO_ADD_TEXT', 'href="' . xnhtmlentities($this->_buildUrl('embed', 'widgets')) . '"') %>
            <?php
                }
            ?>
            </li></ul>
        </div>
    <?php
    } ?>
    <?php if($this->userCanEdit){ ?>
    <input type="hidden" class="html_code" value="<%= xnhtmlentities($this->sourceHtml) %>" /> <?php } ?>
</div>
