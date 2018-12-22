<?php
/*  $Id: $
 *
 *  Display the empty events calendar
 *
 *  @param	$this->subHeader	string
 *  @param	$this->message	string
 *  @param	$this->noAddLink	string
 */
?>
<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
        <?php $this->renderPartial('fragment_navigation','_shared') ?>
        <%=xg_headline($this->title, array('avatarUser' => $this->screenName))%>
        <div class="xg_module">
            <div class="xg_module_body">
                <?php
                    if ($this->subHeader) {
                        echo "<h3>" . xnhtmlentities($this->subHeader) . "</h3>";
                    }
                    echo "<p>" . xnhtmlentities($this->message) . "</p>";
                ?>
                <?php if (!$this->noAddLink) { ?>
                    <p><a class="desc add" href="<%=$this->_buildUrl('event','new')%>"><%=xg_html('ADD_AN_EVENT')%></a></p>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
