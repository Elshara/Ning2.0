<?php
/*  $Id: $
 *
 *  Displays the search results for events
 *
 *	@param	$this->title
 *	@param	$this->searchTerms
 *	@param	$this->wrapDate
 *	@param	$this->eventList		XG_PagingList<Event>
 *	@param	$this->rsvp				hash<event-id:status>
 *	@param	+ _shared/fragment_sideBlock
 */
?>
<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
        <?php $this->renderPartial('fragment_navigation','_shared') ?>
        <%= xg_headline( xg_text('SEARCH_RESULTS'), array( 'count' => $this->eventList->totalCount) ) %>
        <div class="xg_column xg_span-12">
            <?php $this->renderPartial('fragment_search','_shared', array('value' => $this->searchTerms)); ?>
            <?php if (count($this->eventList)) { $this->renderPartial('fragment_list','_shared',array('list'=>$this->eventList, 'wrapDate'=>$this->wrapDate, 'stdPagination' => 1));
            } else { ?>
                <div class="xg_module">
                    <div class="xg_module_body body_events_main">
                        <p><%=xg_html('NO_RESULTS_FOUND_FOR_SEARCH_TERM',xnhtmlentities($this->searchTerms))%></p>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="xg_column xg_span-4 xg_last">
            <?php $this->renderPartial('fragment_sideBlock', '_shared') ?>
        </div>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
