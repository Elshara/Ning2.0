<?php
/*  $Id: $
 *
 *  Renders empty list of notes.
 *
 *  Parameters:
 *  	$this->title
 *		$this->notFoundMsg		string
 *		$this->showSearch		show "search" box.
 *		$this->canCreate		show "create note" link
 */
?>
<?php xg_header(W_Cache::current('W_Widget')->dir, $this->pageTitle); ?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
        <?php $this->renderPartial('fragment_navigation') ?>
        <%= xg_headline($this->title) %>
		<?php if ($this->showSearch) {
			XG_PageHelper::searchBar(array(
				'url' => Notes_UrlHelper::url('search'),
				'buttonText' => xg_html('SEARCH_NOTES'),
			));
		} ?>
        <div class="xg_module">
            <div class="xg_module_body">
                <p><%=$this->notFoundMsg%></p>
                <?php if($this->canCreate) {?>
                    <p><a class="desc add" style="display:none" href="#" dojoType="AddNoteLink" _baseUrl="<%=xnhtmlentities(Notes_UrlHelper::noteUrl(NULL))%>" _maxLength="<%=Note::MAX_TITLE_LENGTH%>"><%=xg_html('ADD_NOTE')%></a></p>
                <?php }?>
            </div>
        </div>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
