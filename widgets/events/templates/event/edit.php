<?php
/*  $Id: $
 *
 *  Create Event page
 *
 *  Parameters:
 *      $this->form	XG_Form
 *      $this->eventId
 *
 */
?>
<?php xg_header(W_Cache::current('W_Widget')->dir, $this->title); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16 first-child">
		<?php $this->renderPartial('fragment_navigation','_shared') ?>
		<%= xg_headline($this->title) %>
        <form id="event_form" action="<%=qh($this->_buildUrl('event','update',array('id'=>$this->eventId)))%>" method="post" enctype="multipart/form-data">
            <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
            <?php echo $this->renderPartial('fragment_eventForm', '_shared', array('form'=>$this->form,'edit'=>1))?>
        </form>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
