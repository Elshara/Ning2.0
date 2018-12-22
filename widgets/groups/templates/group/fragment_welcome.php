<?php
ob_start();
if (Group::userIsCreator($group)) { ?>
    <p><%= xg_html('YOU_HAVE_CREATED_GROUP', xnhtmlentities($group->title)) %></p>
<?php
} else { ?>
    <p><%= xg_html('YOU_CAN_PARTICIPATE_IN_GROUP', xnhtmlentities($group->title)) %></p>
<?php
}
$body = trim(ob_get_contents());
ob_end_clean();
if ($body) { ?>
    <div class="xg_module">
        <div class="xg_module_body success topmsg">
            <h3><%= xg_html('WELCOME_TO_GROUP', xnhtmlentities($group->title)) %></h3>
            <%= $body %>
        </div>
    </div>
<?php
} ?>