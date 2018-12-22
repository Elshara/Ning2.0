<?php

/**
 * Common code for saving and querying Comment objects
 */
class Video_CommentHelper {

    public static function getCommentsFor($videoId, $page, $pageSize) {
        $page = $page ? $page : 1;
        $start = ($page-1) * $pageSize;
        if (isset($_GET['test_comment_count'])) {
            $comments = array();
            for ($i = 0; $i < $_GET['test_comment_count'] - $start; $i++) {
                $comment = XN_Content::create('Comment');
                $comment->description = mt_rand();
                $comments[] = $comment;
            }
            return array('comments' => $comments, 'numComments' => $_GET['test_comment_count'], 'query' => new Video_CommentHelper_TestQuery());
        }
        return Comment::getCommentsFor($videoId, $start, $start + $pageSize);
    }
    
    /**
     * Loads a comment.
     *
     * @param id The id of the comment
     * @return The comment object if it exists, or null
     */
    public static function load($id) {
        return Comment::load($id);
    }

    public static function count($videoId) {
        $results = Comment::getCommentsFor($videoId, $start, $start + $pageSize);
        return $results['numComments'];
    }

}

class Video_CommentHelper_TestQuery {
    public function getTotalCount() {
        return $_GET['test_comment_count'];
    }
}