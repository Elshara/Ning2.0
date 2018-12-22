<?php
/**
 * Used to display the success message
 */
if (isset($_GET['saved']) && $_GET['saved']) {
    echo "<dl class=\"success msg\"><dt>" . xg_html('SUCCESS_EXCLAMATION') . "</dt>";
    echo "<dd><p>" . xg_html('YOUR_CHANGES_HAVE_BEEN_SAVED') . "</p></dd></dl>\n";
}
