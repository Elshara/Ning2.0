<?php
/**
 * A list of file attachments.
 *
 * @param $attachedTo XN_Content|W_Content  An object which may have attachments
 */
XG_App::ningLoaderRequire('xg.shared.PostLink');
if (count(Page_FileHelper::getFileAttachments($attachedTo))) { ?>
    <h4><%= xg_html('ATTACHMENTS') %></h4>
    <ul class="attachments">
        <?php
        foreach (Page_FileHelper::getFileAttachments($attachedTo) as $fileAttachment) {
            $url = $this->_buildUrl('attachment', 'download', array('id' => $fileAttachment['id'])); ?>
            <li>
                <a href="<%= xnhtmlentities($url) %>"><img src="<%= xnhtmlentities(Page_HtmlHelper::attachmentIconUrl($fileAttachment['filename'])) %>" alt="" /></a> <a href="<%= xnhtmlentities($url) %>"><%= xnhtmlentities($fileAttachment['filename']) %></a>, <%= xnhtmlentities(Page_HtmlHelper::fileSizeDisplayText($fileAttachment['sizeInBytes'])) %>
                <?php
                if (Page_SecurityHelper::currentUserCanDeleteAttachments($attachedTo)) { ?>
                    <a class="desc delete" href="#" style="display: none" dojoType="PostLink" _url="<%= xnhtmlentities($this->_buildUrl('attachment', 'delete', array('id' => $fileAttachment['id'], 'attachedTo' => $attachedTo->id, 'target' => XG_HttpHelper::currentUrl()))) %>" _confirmTitle="<%= xg_html('DELETE_ATTACHMENT') %>" _confirmOkButtonText="<%= xg_html('DELETE') %>" _confirmQuestion="<%= xg_html('DELETE_ATTACHMENT_Q') %>"><%= xg_html('DELETE') %></a>
                <?php
                } ?>
            </li>
        <?php
        } ?>
    </ul>
<?php
}
