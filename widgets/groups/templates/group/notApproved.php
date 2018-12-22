<?php
xg_header(W_Cache::current('W_Widget')->dir, xg_text('GROUPS'), null, array('metaDescription' => $this->group->description)); ?>
<div id="xg_body">
    <div class="xg_module_body notification topmsg">
        <p class="last-child"><%= xg_html('GROUP_IS_WAITING_FOR_APPROVAL', xnhtmlentities(XN_Application::load()->name)) %></p>
    </div>
    <?php // TODO: also need to render Our Apologies page for non-group owner / non-admin ?>
    <?php
    ob_start();
    $this->_widget->dispatch('embed', 'embed3pagetitle');
    $this->_widget->dispatch('embed', 'embed2description');
    $aboutBoxHtml = trim(ob_get_contents());
    ob_end_clean();
    if ($aboutBoxHtml) { ?>
        <div class="xg_column xg_span-16 first-child">
            <%= $aboutBoxHtml %>
        </div>
    <?php
    } ?>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>