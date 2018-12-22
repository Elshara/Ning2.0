<?php xg_header(W_Cache::current('W_Widget')->dir, xg_html('APPNAME_REVIEWS', xnhtmlentities($this->appName)));  ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
        <%= xg_headline(xg_html('APPNAME_REVIEWS', $appName), array('byline1Html' => '<p>' . xg_html('BACK_TO_APPNAME_LINK', 'href="' . xnhtmlentities($this->aboutPageUrl) . '" class="desc back"', xnhtmlentities($this->appName)) . '</p>')) %>

        <%= $this->renderPartial('fragment_reviews', 'application', array('appUrl' => $this->appUrl)) %>
    </div>
    <div class="xg_column xg_span-4 xg_last">        
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
