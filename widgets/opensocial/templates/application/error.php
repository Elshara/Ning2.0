<?php
/**
 * Template that displays error message when displaying on the page it relates to is inappropriate.
 */
xg_header($this->gadget->ownerName == $this->gadget->viewerName ? 'profile' : 'members', $this->title); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
        <%= xg_headline(xg_text('ERROR')) %>
        <div class="xg_module">
            <?php $this->renderPartial('fragment_errorMsg', 'application'); ?>
        </div>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer();
