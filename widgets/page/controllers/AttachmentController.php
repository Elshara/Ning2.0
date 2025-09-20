<?php
XG_App::includeFileOnce('/lib/XG_HttpHelper.php');

/**
 * Dispatches requests pertaining to file attachments.
 */
class Page_AttachmentController extends W_Controller {

    protected function _before() {
        $this->_widget->includeFileOnce('/lib/helpers/Page_SecurityHelper.php');
    }


    /**
     * Redirects to the URL for the given file.
     *
     * Expected GET variables:
     *     id - ID of the UploadedFile object
     */
    public function action_download() {
        $attachment = XN_Content::load($_GET['id']);
        if ($attachment->type != 'UploadedFile') { throw new Exception('Not an UploadedFile'); }
        header('Location: ' . $attachment->fileUrl('data'));
    }

    /**
     * Deletes the attachment, then redirects to the target.
     *
     * Expected GET variables:
     *     id - ID of the UploadedFile object to delete
     *     attachedTo - ID of the Comment or Page that the file is attached to
     *     target - URL to redirect to
     */
    public function action_delete() {
        $this->_widget->includeFileOnce('/lib/helpers/Page_FileHelper.php');
        $this->_widget->includeFileOnce('/lib/helpers/Page_SecurityHelper.php');
        if ($_SERVER['REQUEST_METHOD'] != 'POST') { throw new Exception('Not a POST'); }
        $attachment = XN_Content::load($_GET['id']);
        if ($attachment->type != 'UploadedFile') { throw new Exception('Not an UploadedFile'); }
        $attachedTo = XN_Content::load($_GET['attachedTo']);
        if ($attachedTo->type != 'Page' && $attachedTo->type != 'Comment') { throw new Exception('Not a Page or Comment'); }
        if (! Page_SecurityHelper::currentUserCanDeleteAttachments($attachedTo)) { throw new Exception('Not allowed'); }
        Page_FileHelper::deleteAttachment($attachment, $attachedTo);
        $attachedTo->save();

        $redirectTarget = $this->getSanitizedRedirectTarget();
        if ($redirectTarget === null) {
            $redirectTarget = $this->buildAttachmentRedirect($attachedTo);
        }

        header('Location: ' . $redirectTarget);
    }

    private function getSanitizedRedirectTarget(): ?string
    {
        if (! isset($_GET['target']) || is_array($_GET['target'])) {
            return null;
        }

        return XG_HttpHelper::normalizeRedirectTarget($_GET['target']);
    }

    private function buildAttachmentRedirect(XN_Content $attachedTo): string
    {
        if ($attachedTo->type === 'Page') {
            return W_Cache::current('W_Widget')->buildUrl('page', 'show', array('id' => $attachedTo->id));
        }

        return W_Cache::current('W_Widget')->buildUrl('page', 'show', array('id' => $attachedTo->my->attachedTo));
    }

}
