<?php
/*  $Id: $
 *
 *  Displays the list of all event types
 *
 *	@param	$this->title
 *	@param	$this->eventTypes
 *	@param	$this->calendar
 */
arsort($this->eventTypes,SORT_NUMERIC);
if ($this->displayMode == 'home') {
    $urlPrefix = $this->_buildUrl('event','listByType','?type=');
} elseif ($this->displayMode == 'user' || $this->displayMode == 'my') {
    $urlPrefix = $this->_buildUrl('event','listUserEventsByType','?user='.urlencode($this->screenName).'&type=');
}
?>
<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
        <?php $this->renderPartial('fragment_navigation','_shared') ?>
        <%=xg_headline($this->title, array('count' => count($this->eventTypes), 'avatarUser' => $this->screenName))%>
        <div class="xg_column xg_span-12">
	        <?php $this->renderPartial('fragment_search','_shared'); ?>
            <div class="xg_module">
                <div class="xg_module_body">
                    <?php if (count($this->eventTypes)) {
                        echo '<ul class="nobullets">';
                        foreach($this->eventTypes as $name=>$cnt) {
                            echo '<li><a href="',$urlPrefix,urlencode($name),'">',xnhtmlentities($name),'</a>', "($cnt)","</li>\n";
                        }
                        echo '</ul>';
                    } else {
                        echo xg_html('THERE_ARE_NO_TYPES_YET');
                    }?>
                </div>
            </div>
            <?php
            ?>
        </div>
        <div class="xg_column xg_span-4 xg_last">
            <?php $this->renderPartial('fragment_sideBlock','_shared', array('noEventTypes'=>1)) ?>
        </div>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
