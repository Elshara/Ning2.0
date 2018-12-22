<?php
/**
 * A list of file attachments.
 *
 * @param $attachedTo XN_Content|W_Content  An object which may have attachments
 */ ?>
 <dt><%= xg_html('ATTACHMENTS') %></dt>
 <dd>
    <ul class="attachments">
        <?php
        foreach (Forum_FileHelper::getFileAttachments($attachedTo) as $fileAttachment) {
            $url = $this->_buildUrl('attachment', 'download', array('id' => $fileAttachment['id'])); ?>
            <li>
                <a href="<%= xnhtmlentities($url) %>"><img src="<%= xnhtmlentities(Forum_HtmlHelper::attachmentIconUrl($fileAttachment['filename'])) %>" alt="" /></a> <a href="<%= xnhtmlentities($url) %>"><%= xnhtmlentities($fileAttachment['filename']) %></a>, <%= xnhtmlentities(Forum_HtmlHelper::fileSizeDisplayText($fileAttachment['sizeInBytes'])) %>
                <?php
                if (Forum_SecurityHelper::currentUserCanDeleteAttachments($attachedTo)) { ?>
                    &#160; <a class="desc delete" href="#" dojoType="PostLink" _url="<%= xnhtmlentities($this->_buildUrl('attachment', 'delete', array('id' => $fileAttachment['id'], 'attachedTo' => $attachedTo->id))) %>" _confirmTitle="<%= xg_html('DELETE_ATTACHMENT') %>" _confirmOkButtonText="<%= xg_html('DELETE') %>" _confirmQuestion="<%= xg_html('DELETE_ATTACHMENT_Q') %>"><%= xg_html('DELETE') %></a>
                <?php
                } ?>
            </li>
        <?php
        } ?>
    </ul>
</dd>
