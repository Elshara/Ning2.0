<?php
/*	$Id: $
 *
 *	Prev/next pagination
 *
 *	Parameters:
 *		$list				XG_PagingList<Event>
 */
?>
<ul class="pagination smallpagination">
<?php if (!$list->isFirstPage()) {?>
	<li class="left"><a href="<%=$list->prevPageUrl()%>">&lt; <%=xg_html('PREVIOUS')%></a></li>
<?php } ?>
<?php if (!$list->isLastPage()) {?>
	<li class="right"><a href="<%=$list->nextPageUrl()%>"><%=xg_html('NEXT')%> &gt;</a></li>
<?php }?>
</ul>
