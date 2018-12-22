<?php
/**	$Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *	Notes template helpers
 *
 **/
class Notes_TemplateHelper {
    // Returns string describing note information (creation, updates)
    public function noteInfo($note, $updateOnly = false) { # void
        $info = '';
        if (! $updateOnly) {
            $info .= xg_html('NOTE_CREATED',
                                xg_date(xg_html('NOTE_DATE'),$note->createdDate),
                                '<a href="'.xnhtmlentities(User::quickProfileUrl($note->contributorName)).'">'.xnhtmlentities(xg_username($note->contributorName)).'</a>') . ' ';
        }
        $info .= xg_html('NOTE_UPDATED',
                                xg_elapsed_time($note->updatedDate),
                                '<a href="'.xnhtmlentities(User::quickProfileUrl($note->my->lastUpdatedBy)).'">'.xnhtmlentities(xg_username($note->my->lastUpdatedBy)).'</a>');
        return $info;
    }

    /**
     *  Excerpts the Note's description if necessary.
     *
     *  @param  $note       XN_Content  The Note
     *  @param  $maxLength  integer     The maximum length of the description before truncation
     *  @return             string      The Note's description HTML, possibly excerpted
     */
    public function excerpt($note, $maxLength) {
        if (mb_strlen($note->description) <= $maxLength) { return $note->description; }
        $description = xg_excerpt_html($note->description, $maxLength, $excerpted);
        if ($excerpted) { $description .= ' <p><a href="' . xnhtmlentities(Notes_UrlHelper::noteUrl($note)) . '">' . xg_html('CONTINUE') . '</a></p>'; }
        return $description;
    }

}
?>
