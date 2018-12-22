<?php
/*  $Id: $
 *
 *  Nagivation block
 *
 *  Parameters:
 *  	$this->searchQuery
 *  	$this->canCreate
 *  	$noAddLink			suppress the "add note" link
 */
?>
<?php if ($this->canCreate && !$noAddLink) {
	$this->renderPartial('fragment_addNoteForm','index');
}?>
<ul class="navigation">
    <li><a href="<%=Notes_UrlHelper::noteUrl('')%>"><%=xg_html('NOTES_HOME')%></a></li>
    <li><a href="<%=Notes_UrlHelper::url('allNotes')%>"><%=xg_html('ALL_NOTES')%></a></li>
    <?php if ($this->canCreate && !$noAddLink) {?>
        <li class="right"><a class="desc add" style="display:none" href="#" dojoType="AddNoteLink" _baseUrl="<%=xnhtmlentities(Notes_UrlHelper::noteUrl(NULL))%>" _maxLength="<%=Note::MAX_TITLE_LENGTH%>"><%=xg_html('ADD_NOTE')%></a></li>
    <?php }?>
</ul>
