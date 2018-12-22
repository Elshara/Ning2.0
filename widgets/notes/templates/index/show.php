<?php
/*  $Id: $
 *
 *  Displays note
 *
 *  Parameters:
 *      $this->pageTitle        page title
 *  	$this->title            heading title
 *  	$this->showEditBox	show "edit" confirmation box
 *		$this->noteTitle	Title for unexisting note
 *		$this->noteContent	Note content
 *		$this->canCreate
 *
 *		$this->note 		Note:
 *			$this->canRead				can view it
 *			$this->canFeature			can feature it
 *			$this->canEdit				can edit it
 *			$this->canDelete			can delete it
 */
?>
<?php xg_header(W_Cache::current('W_Widget')->dir, $this->title, NULL, array(
	'metaDescription' => $this->noteContent
)); ?>
<?php if ($this->isMain) {
     xg_autodiscovery_link($this->_buildUrl('index','feed', array('xn_auth' => 'no')), xg_text('MOST_RECENTLY_UPDATED'), 'atom');
} ?>
<?php
if ($this->showEditBox && $this->note) {
    XG_App::ningLoaderRequire('xg.shared.util');
?>
<script type="text/javascript">
    xg.addOnRequire(function() {
        xg.shared.util.confirm({
            title: <%=json_encode(xg_html('EDIT_NOTE'))%>,
            bodyHtml: <%=json_encode(xg_html('NOTE_EXISTS',xnhtmlentities($this->note->title)))%>,
            okButtonText: <%=json_encode(xg_html('EDIT'))%>,
            onOk: function () {
                window.location = <%=json_encode(Notes_UrlHelper::noteUrl($this->note,'edit'))%>;
            }
        });
    });
</script>
<?php }?>
<div id="xg_body">
    <div class="xg_column xg_span-16">
        <?php
               $this->renderPartial('fragment_navigation', array('canCreate' => $this->canCreate));
               $contributor = $this->note->contributorName ? XG_Cache::profiles($this->note->contributorName) : NULL;
               $date = xg_date(xg_text('F_J_Y'), $this->note->createdDate);
               $time = xg_date(xg_text('G_IA'), $this->note->createdDate);
        ?>
        <%=xg_headline($this->pageTitle, array(
				'avatarUser' => $contributor,
				'byline1Html' => $contributor ? xg_html('ADDED_BY_USER_ON_DATE_AT_TIME', xg_userlink($contributor), xnhtmlentities($date), xnhtmlentities($time)) : ''
				# BAZ-10884: Commenting this line out since Notes never has the "View xxx" link that other Mozzles have.
                # 'byline2Html' => xg_message_and_friend_links($this->note->contributorName)
				))%>
        <div class="xg_module">
            <?php if ($this->note) {?>
                <div class="xg_module_body">
                    <div class="notes_body">
                        <?php if ( $this->canFeature || $this->canEdit || $this->canDelete ) { $this->renderPartial('fragment_adminBox'); }?>
                        <%=$this->noteContent%>
                    </div>
                </div>
                <?php if ($this->canRead) {?>
                    <div class="xg_module_foot"><p><%=Notes_TemplateHelper::noteInfo($this->note, true)%></p></div>
                <?php }?>
            <?php } else {?>
                <div class="xg_module_body">
                    <div class="notes_body">
                        <p><%=$this->noteContent%></p>
                        <?php if ($this->canCreate) {?>
                            <p><a class="desc edit" href="<%=Notes_UrlHelper::noteUrl($this->noteTitle,'edit')%>"><%=xg_html('CREATE_THIS_NOTE')%></a></p>
                        <?php }?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="xg_column xg_span-4 last-child">
        <?php xg_sidebar($this); ?>
    </div>
</div>
<?php xg_footer(); ?>
