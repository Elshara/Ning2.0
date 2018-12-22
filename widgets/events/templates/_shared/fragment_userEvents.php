<?php
/*  $Id: $
 *
 *  Display my upcoming events list
 *
 *  Parameters:
 *		$list		XG_PagingList<Event>
 *		$title
 *		$viewAllUrl
 */
?>
<div class="xg_module eventmodule">
    <div class="xg_module_head">
        <h2><%=xnhtmlentities($title)%></h2>
    </div>
    <div class="xg_module_body">
        <ul class="nobullets">
<?php foreach ($list as $e) {?>
            <li><a href="<%=$this->_buildUrl('event','show',"?id=$e->id")%>"><%=xnhtmlentities($e->title)%></a><br/><%=Events_TemplateHelper::startDate($e,TRUE)%></li>
<?php }?>
        </ul>
        <p class="right"><small><a href="<%=$viewAllUrl%>"><%=xg_html('VIEW_ALL')%></a></small></p>
    </div>
</div>
