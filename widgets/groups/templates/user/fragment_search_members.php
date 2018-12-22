<?php
/**
 * Fragment to display search form for searching within currently displayed members and Show All button to clear current search.
 *
 * TODO can we share the identical groups and index copies of this fragment?
 */
?>
<p class="right">
    <small>
        <form method="get" style="display: inline">
        <input type="text" class="textfield" name="q" value="<%= xnhtmlentities($_GET['q']) %>" />
        <input type="submit" class="button" value="<%= xg_html('SEARCH') %>" />
        </form>
        <form method="get" style="display: inline">
        <input type="submit" class="button" value="<%= xg_html('SHOW_ALL') %>" />
        </form>
    </small>
</p>
