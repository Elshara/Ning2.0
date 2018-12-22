<ul class="page_tabs">
    <?php
    $first = true;
    foreach ($this->tabs as $tab) {
        if (! $tab['count'] && ! $first) { continue; }
        $first = false; ?>
        <li <%= $tab['text'] == $this->currentTab ? 'class="this"' : '' %>>
            <%= $tab['text'] == $this->currentTab ? '<span class="xg_tabs">' : '<a href="' . xnhtmlentities($tab['url']) . '">' %>
            <%= xnhtmlentities($tab['text']) %> <small>(<%= xnhtmlentities($tab['count']) %>)</small>
            <%= $tab['text'] == $this->currentTab ? '</span>' : '</a>' %>
        </li>
    <?php
    }
    if (XG_App::canSeeInviteLinks(XN_Profile::current())) { ?>
    <li class="right">
        <?php if ($this->groupInviteLink) { ?>
            <a href="<%= xnhtmlentities($this->groupInviteLink) %>" class="desc add"><%= xg_html('INVITE_MORE_PEOPLE') %></a>
        <?php } else { ?>
            <a href="/invite" class="desc add"><%= xg_html('INVITE_MORE_PEOPLE') %></a>
        <?php } ?>
    </li>
    <?php } ?>
</ul>
