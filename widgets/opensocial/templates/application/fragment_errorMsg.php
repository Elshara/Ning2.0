<?php        
$errorMsg = (isset($_GET['error']) ? xg_text(qh($_GET['error'])) : '');
if ($errorMsg) { ?>
    <p class="errordesc msg">
        <%= qh($errorMsg) %>
    </p>
<?php }
