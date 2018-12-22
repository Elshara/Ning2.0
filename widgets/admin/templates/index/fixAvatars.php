<?php
if ($this->screenNamesFixed) { ?>
    <p><i>Fixed: <%= xnhtmlentities($this->screenNamesFixed) %></i></p>
<?php
} ?>
<form action="<%= $this->_widget->buildUrl('index', 'doFixAvatars') %>" method="post">
    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
    <h1>Fix for BAZ-5031</h1>
    <p>Enter the screen-names of users whose avatars to fix (separated by commas):</p>
    <p><input type="text" name="screenNames" style="width:500px" /></p>
    <p><input type="submit" value="Fix" /></p>
</form>
