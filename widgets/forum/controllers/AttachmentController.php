<?php

/**
 * Dispatches requests pertaining to file attachments.
 */
class Forum_AttachmentController extends XG_GroupEnabledController {

    /**
     * Redirects to the URL for the given file.
     *
     * Expected GET variables:
     *     id - ID of the UploadedFile object
     */
    public function action_download() {
        $attachment = XG_GroupHelper::checkCurrentUserCanAccess(XN_Content::load($_GET['id']));
        if ($attachment->type != 'UploadedFile') { throw new Exception('Not an UploadedFile'); }
        header('Location: ' . $attachment->fileUrl('data'));
    }

    /**
     * Deletes the attachment, then redirects to the target.
     *
     * Expected GET variables:
     *     id - ID of the UploadedFile object to delete
     *     attachedTo - ID of the Comment or Topic that the file is attached to
     */
    public function action_delete() {
        $this->_widget->includeFileOnce('/lib/helpers/Forum_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Forum_SecurityHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        $attachment = XG_GroupHelper::checkCurrentUserCanAccess(XN_Content::load($_GET['id']));
        if ($attachment->type != 'UploadedFile') { throw new Exception('Not an UploadedFile'); }
        $attachedTo = XG_GroupHelper::checkCurrentUserCanAccess(XN_Content::load($_GET['attachedTo']));
        if ($attachedTo->type != 'Topic' && $attachedTo->type != 'Comment') { throw new Exception('Not a Topic or Comment'); }
        if (! Forum_SecurityHelper::currentUserCanDeleteAttachments($attachedTo)) { throw new Exception('Not allowed'); }
        Forum_FileHelper::deleteAttachment($attachment, $attachedTo);
        $attachedTo->save();
        header('Location: ' . $this->buildAttachedContentUrl($attachedTo));
    }

    private function buildAttachedContentUrl(XN_Content $attachedTo): string
    {
        return xg_absolute_url('/xn/detail/' . rawurlencode($attachedTo->id));
    }

}
