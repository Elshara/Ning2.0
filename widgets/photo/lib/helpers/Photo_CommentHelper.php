<?php

/**
 * Common code for saving and querying Comment objects
 */
class Photo_CommentHelper {

    /**
     * Loads a comment.
     *
     * @param id The id of the comment
     * @return The comment object if it exists, or null
     */
    public static function load($id) {
        return Comment::load($id);
    }

    /**
     * Returns all comments for the indicated photo.
     *
     * @param photoId The id of the photo whose comments to return
     * @param begin   The index of the first comment to return
     * @param end     The index of comment after the last comment to return
     * @return An array 'comments' => the comments, 'numComments' => the total number of comments that match the query
     */
    public static function getCommentsFor($photoId, $begin = 0, $end = 100) {
        if (isset($_GET['test_comment_count'])) {
            $comments = array();
            for ($i = 0; $i < min($end-$begin, $_GET['test_comment_count'] % ($end-$begin)); $i++) {
                $comment = XN_Content::create('Comment');
                $comment->description = mt_rand();
                $comments[] = $comment;
            }
            return array('comments' => $comments, 'numComments' => $_GET['test_comment_count']);
        }
        return Comment::getCommentsFor($photoId, $begin, $end, null);
    }
}
