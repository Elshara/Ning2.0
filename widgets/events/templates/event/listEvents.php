<?php
/*  $Id: $
 *
 *  Display the list of events
 *
 *	@param	$this->title
 *	@param	$this->wrapDate
 *	@param	$this->featuredEvents	XG_PagingList<Event>
 *	@param	$this->featuredEvent	Event (featured event to show)
 *	@param	$this->eventList		XG_PagingList<Event>
 *	@param	$this->rsvp				hash<event-id:status>
 *	@param	+ _shared/fragment_sideBlock
 */
?>
<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
        <?php $this->renderPartial('fragment_navigation','_shared') ?>
        <?php if (count($this->featuredEvents) && (!isset($_GET['page']) || $_GET['page'] == 1)) $this->renderPartial('fragment_featured','_shared', array( 'list' => $this->featuredEvents, 'showViewAll' => 1 )) ?>
		<%= xg_headline($this->title, array('count' => $this->eventList->totalCount, 'avatarUser' => $this->screenName))%>
        <div class="xg_column xg_span-12">
	        <?php if (!$this->noSearch) { $this->renderPartial('fragment_search','_shared'); } ?>
            <?php if ($this->featuredEvent) { $this->renderPartial('fragment_featuredEvent', '_shared', array('event'=>$this->featuredEvent)); } ?>
            <?php if (count($this->eventList)) { $this->renderPartial('fragment_list','_shared',array('list'=>$this->eventList, 'wrapDate'=>$this->wrapDate, 'feedUrl'=>$this->feedUrl));
            } elseif (! $this->featuredEvent) {?>
                <div class="xg_module">
                    <div class="xg_module_body">
                        <p><%=xg_html('NO_MATCHING_EVENTS_FOUND')%></p>
                        <?php if (Events_SecurityHelper::currentUserCanCreateEvent()) { ?>
                            <p><a class="bigdesc add" href="<%=$this->_buildUrl('event','new')%>"><%=xg_html('ADD_EVENTS')%></a></p>
                        <?php } ?>
                    </div>
                </div>
            <?php
                // It's required for by-date listings.
                $this->renderPartial('fragment_pagination','_shared', array('list' => $this->eventList));
            } ?>
        </div>
        <div class="xg_column xg_span-4 xg_last">
            <?php $this->renderPartial('fragment_sideBlock','_shared') ?>
        </div>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
