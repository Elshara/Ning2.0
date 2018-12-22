<?php
if (isset($_GET['shareInvitesSent']) && $_GET['shareInvitesSent']) { ?>
    <div class="xg_module">
        <div class="xg_module_body success">
            <p class="last-child"><%= xg_html($_GET['shareInvitesSent'] > 1
                    ? 'YOUR_MESSAGES_SENT' : 'YOUR_MESSAGE_SENT') %></p>
        </div>
    </div>
<?php
} ?>
