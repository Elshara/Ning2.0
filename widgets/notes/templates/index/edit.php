<?php
/*  $Id: $
 *
 *  Edits notes
 *
 *  Parameters:
 *  	$this->title				string		Note title
 *		$this->noteKey              string		Note key
 *		$this->noteContent			string		The content of the existing note or empty string for a new note
 *		$this->noteVersion			int			The version at this moment or 0 for a new note.
 *		$this->noteVisibility		string		Current note visibility
 *		$this->isMain				bool		Is this Notes home?
 */
?>
<?php xg_header(W_Cache::current('W_Widget')->dir, $this->title); ?>
<div id="xg_body">
	<div class="xg_column xg_span-16">
		<?php $this->renderPartial('fragment_navigation',array('noAddLink' => true)) ?>
		<?php $this->renderPartial('fragment_editor') ?>
	</div>
	<div class="xg_column xg_span-4 last-child">
		<?php xg_sidebar($this); ?>
	</div>
</div>
<script language='javascript' src='/xn_resources/widgets/notes/editor/com.ning.NoteEditor.nocache.js'></script>
<?php XG_App::ningLoaderRequire('xg.notes.NoteEditor','xg.shared.AddImageDialog');?>
<?php xg_footer(); ?>
