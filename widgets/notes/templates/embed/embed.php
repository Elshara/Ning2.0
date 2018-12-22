<?php
/*  $Id: $
 *
 *  Displays embed module
 *
 *  Parameters:
 *
 *		$this->isHomepage
 *  	$this->isOwner
 *			$this->form			XG_Form
 *			$this->columns		int			the number of columns
 *			$this->setValuesUrl	string
 *			$this->settings		hash
 *
 *		$this->hasContent	bool
 *		$this->content		Note|XG_PagingList<Note>
 *		$this->viewAllUrl	string
 */
if ($this->isOwner) { ?>
<div class="xg_module module_notes" dojoType="NoteEmbedModule" _url="<%=xnhtmlentities($this->setValuesUrl)%>" _isHomepage="<%=$this->isHomepage?1:0%>">
    <div class="xg_module_head">
        <h2><%=xg_html('NOTES')%></h2>
        <p class="edit" style="display:none"><a class="button" href="#"><%=xg_html('EDIT')%></a></p>
    </div>
<?php
	XG_App::ningLoaderRequire('xg.notes.NoteEmbedModule');
    $this->renderPartial('fragment_form','embed', array('form' => $this->form, 'columns' => $this->columns, 'isHomepage' => $this->isHomepage));
} elseif (!$this->hasContent) {
    return; // nothing to do
} else {?>
<div class="xg_module module_notes">
    <div class="xg_module_head">
        <h2><%=xg_html('NOTES')%></h2>
    </div>
<?php } ?>
    <?php $this->renderPartial('fragment_block','embed', array(
        'content'	=> $this->content,
        'settings'	=> $this->settings,
        'columns' 	=> $this->columns,
        'viewAllUrl'=> $this->viewAllUrl,
    ));?>
</div>
<?php XG_App::addToCssSection('<link rel="stylesheet" type="text/css" media="screen,projection" href="'.xnhtmlentities(XG_Version::addXnVersionParameter($this->_widget->buildResourceUrl('css/module.css'))).'" />');?>
