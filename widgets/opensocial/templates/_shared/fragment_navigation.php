<?php
if(XN_Profile::current()->isLoggedIn()){ ?>
    <ul class="navigation">
        <?php
        if (XG_App::canSeeInviteLinks(XN_Profile::current())) { ?>
            <li class="right"><strong><a href="<%= xnhtmlentities('/invite') %>" class="bigdesc add"><%= xg_html('INVITE_FRIENDS') %></a></strong></li>
        <?php
        } ?>
    </ul>
<?php
}
