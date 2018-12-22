<?php
$title = $this->error ? $this->error['title'] : xg_text('YIKES');
$subtitle = $this->error ? $this->error['subtitle'] : xg_text('A_PROBLEM_SEEMS');
$description = $this->error ? $this->error['description'] : ''; ?>

<?php xg_header(W_Cache::current('W_Widget')->dir, $title); ?>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_colgroup">
            <div class="xg_3col first-child">
				<?php XG_PageHelper::subMenu(Photo_HtmlHelper::subMenu($this->_widget)) ?>
				<%= xg_headline($title)%>
                <div class="xg_module">
                    <div class="xg_module_body pad">
                        <h3><%= xnhtmlentities($subtitle) %></h3>
                        <p><%= xnhtmlentities($description) %> <%= xg_html('FOR_FURTHER_ASSISTANCE', 'href="http://www.ning.com/help/feedback.html"') %></p>
                        <p>
                            <%= xg_html('OR_YOU_CAN_GO', 'href="/"', xnhtmlentities(XN_Application::load()->name), 'href="http://browse' . XN_AtomHelper::$DOMAIN_SUFFIX . '"') %>.

                    </div>
                </div>
            </div>
            <div class="xg_1col last-child">
                <?php xg_sidebar($this); ?>
            </div>
        </div>
    </div>
</div>
<?php xg_footer(); ?>
