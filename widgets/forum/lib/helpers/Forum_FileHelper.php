<?php

/**
 * Useful functions for working with files and uploads.
 */
class Forum_FileHelper {

    /**
     * Associates an uploaded file with a content object.
     * Be sure to save the $attachedTo object afterwards.
     *
     * @param $postVariableName string  The name of the file field containing the uploaded file.
     * @param $attachedTo XN_Content|W_Content  The object with which to associate the uploaded file
     */
    public static function addAttachment($postVariableName, $attachedTo) {
        list($attachment, $filename, $sizeInBytes) = XG_FileHelper::createUploadedFileObject($postVariableName);
        self::addAttachmentProper($attachment->id, $filename, $sizeInBytes, $attachedTo);
    }

    /**
     * Deletes a file attachment, and severs its association with the given object.
     * Be sure to save the $attachedTo object afterwards, as its attributes will be updated.
     *
     * @param $attachment XN_Content|string  The UploadedFile object, or its ID
     * @param $attachedTo XN_Content|W_Content  The object to which the file is attached
     */
    public static function deleteAttachment($attachment, $attachedTo) {
        $attachment = is_object($attachment) ? $attachment : XN_Content::load($attachment);
        $id = $attachment->id;
        XN_Content::delete(W_Content::unwrap($attachment));
        $widget = W_Cache::current('W_Widget');
        $fileAttachments = self::getFileAttachments($attachedTo);
        foreach ($fileAttachments as $fileAttachment) {
            if ($id != $fileAttachment['id']) { $newFileAttachments[] = $fileAttachment; }
        }
        $attachedTo->my->set(self::fileAttachmentsAttrName(), serialize($newFileAttachments));
    }

    /**
     * Deletes the files attached to the given object.
     * Be sure to save the $attachedTo object afterwards, as its attributes will be updated.
     *
     * @param $attachedTo XN_Content|W_Content  The object to which the file is attached
     */
    public static function deleteAttachments($attachedTo) {
        foreach (self::getFileAttachments($attachedTo) as $attachment) {
            self::deleteAttachment($attachment['id'], $attachedTo);
        }
    }

    /**
     * Returns the attribute name for storing file-attachment metadata.
     *
     * @return string  the attribute name appropriate for the current widget
     */
    private static function fileAttachmentsAttrName() {
        return XG_App::widgetAttributeName(W_Cache::current('W_Widget'), 'fileAttachments');
    }

    /**
     * Returns metadata for the files attached to the given object.
     *
     * @param $attachedTo XN_Content|W_Content  The object with which the files are associated
     * @return array  arrays, each containing id, filename, and sizeInBytes
     */
    public static function getFileAttachments($attachedTo) {
        $widget = W_Cache::current('W_Widget');
        if (! $attachedTo->my->raw(self::fileAttachmentsAttrName())) { return array(); }
        $fileAttachments = unserialize($attachedTo->my->raw(self::fileAttachmentsAttrName()));
        return $fileAttachments ? $fileAttachments : array();
    }

    /**
     * Associates an uploaded file with a content object
     *
     * @param $id string The ID of the content object containing the data for the file.
     * @param $filename string  The filename, without path.
     * @param $sizeInBytes int  The number of bytes in the file.
     * @param $attachedTo XN_Content|W_Content  The object with which to associate the uploaded file
     */
    public static function addAttachmentProper($id, $filename, $sizeInBytes, $attachedTo) {
        $fileAttachments = self::getFileAttachments($attachedTo);
        $fileAttachments[] = array('id' => $id, 'filename' => $filename, 'sizeInBytes' => $sizeInBytes);
        $attachedTo->my->set(self::fileAttachmentsAttrName(), serialize($fileAttachments));
    }


}
