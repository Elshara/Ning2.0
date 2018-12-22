<?php
/*  $Id: $
 *
 *  _
 *
 *  Parameters:
 *		$event
 *
 */
?>
<div class="xg_module body_events_feature-single">
    <div class="xg_module_body">
        <h3><%= xg_html('FEATURED_EVENT') %></h3>
        <ul class="clist">
<?php
        $this->renderPartial('fragment_listItem','_shared',array('event'=>$event,'featuredMode'=>1));
?>
        </ul>
    </div>
</div>
