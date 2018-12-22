<?php
/** This partial template renders the "next" button that are displayed at the bottom of some
 * pages during the join-the-app flow
 *
 */
?>
<div class="right clear">
  <ul class="setup_nav">
  <li class="next"><big><a href="javascript:void(0)" id="xg_join_next_a"><%= isset($this->nextLabel) ? $this->nextLabel : xg_html('NEXT') %></a></big></li>
  <script type="text/javascript">xg.addOnRequire(function(){
	dojo.event.connect(dojo.byId('xg_join_next_a'), 'onclick', function(evt) { xg_handleJoinFlowSubmit(evt); });
  });</script>
  </ul>
</div>
