<?php
/*  $Id: $
 *
 *  Renders "admin box"
 *
 *  Parameters:
 *		$this->note
 *		$this->canFeature
 *		$this->canEdit
 *		$this->canDelete
 */
?>
<div class="adminbox xg_module xg_span-4 adminbox-right">
    <div class="xg_module_head"><h2><%=xg_html('ADMIN_OPTIONS')%></h2></div>
    <div class="xg_module_body">
        <ul class="nobullets last-child">
            <?php if ($this->canFeature) {?>
                <li>
                <?php if (XG_PromotionHelper::isPromoted($this->note)) { ?>
					<a class="desc feature-remove" href="<%=xnhtmlentities(Notes_UrlHelper::noteUrl($this->note,'setFeatured','featured=0'))%>"><%=xg_html('REMOVE_FROM_HOME')%></a>
                <?php } else {?>
                    <a class="desc feature-add" href="<%=xnhtmlentities(Notes_UrlHelper::noteUrl($this->note,'setFeatured','featured=1'))%>"><%=xg_html('FEATURE_ON_HOME')%></a>
                <?php }?>
                </li>
            <?php }?>
            <?php if ($this->canEdit) {?><li><a class="desc edit" href="<%=xnhtmlentities(Notes_UrlHelper::noteUrl($this->note,'edit'))%>"><%=xg_html('EDIT')%></a></li><?php }?>
            <?php if ($this->canDelete) {?><li><a href="#" class="desc delete" dojoType="PostLink"
                _confirmTitle="<%=xg_html('DELETE_NOTE') %>"
                _confirmQuestion="<%=xg_html('ARE_YOU_SURE_DELETE_NOTE') %>"
                _url="<%=Notes_UrlHelper::noteUrl($this->note,'delete') %>"
                _reload="false"><%= xg_html('DELETE_NOTE') %></a></li><?php }?>
        </ul>
    </div>
</div>
