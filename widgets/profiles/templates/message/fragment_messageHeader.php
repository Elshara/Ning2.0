<?php
/**
 * Displays one of the messages in a list of messages.
 *
 * @param message XN_Message  the message details to show
 * @param showUrl string  the link to view the message details (not escaped)
 * @param otherPartyProfile XN_Profile|null  XN_Profile for one of the parties other than the current user
 * @param otherPartyScreenNameOrEmail string  screen name or email address of the other party
 * @param otherPartyHasProfile  whether the other party has an XN_Profile (is/was a Ning user)
 * @param numRecipients integer  the number of recipients of the message
 * @param folder string  the current folder the user is viewing
 * @param selected boolean  should the message be pre-selected?
 * @param last boolean  is this message the last row to be displayed on a page?
 */ ?>
<tr class="xg_lightborder<%= $last ? ' last-child' : '' %>">
    <td class="first-child"><input type="checkbox" id="<%= $message->id %>" _sender="<%= xnhtmlentities($message->sender) %>" name="xg_message"<%= ! $message->read ? ' class="unread"' : '' %><%= $selected ? ' checked="checked"' : '' %> /></td>
    <td>
        <?php $unread = (! $message->read) && ($folder !== Profiles_MessageHelper::FOLDER_NAME_SENT);
        if ($unread) { ?>
            <img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/icon/new-flag.png') %>" width="16" height="16" alt="" />
        <?php } elseif ($message->hasReplies) { ?>
            <img src="<%= xg_cdn('/xn_resources/widgets/index/gfx/icon/replied.png') %>" width="16" height="16" alt="" />
        <?php } else { ?>
            &nbsp;
        <?php } ?>
    </td>
    <td><?php if (! is_null($otherPartyProfile)) { echo xg_avatar($otherPartyProfile, 32) ?><?php } ?></td>
    <td>
        <%= $unread ? '<strong>' : '' %><%= $otherPartyHasProfile ? xg_userlink($otherPartyProfile) : xnhtmlentities($otherPartyScreenNameOrEmail) %><%= ($folder === Profiles_MessageHelper::FOLDER_NAME_SENT) && ($numRecipients > 1) ? ' ' . xg_html('AND_N_MORE', $numRecipients - 1) : '' %><%= $unread ? '</strong>' : '' %><br />
        <small class="xg_lightfont"><%= xg_elapsed_time($message->createdDate) %></small>
    </td>
    <td>
        <a href="<%= $showUrl %>">
        <%= $unread ? '<strong>' : '' %><%= xnhtmlentities(Profiles_MessageHelper::formatSubjectForDisplay(xg_excerpt($message->subject, Profiles_MessageHelper::MAX_SUBJECT_DISPLAY_LENGTH))) %><%= $unread ? '</strong>' : '' %><br />
            <span class="xg_bodytexgcolor">
                <%= xnhtmlentities(xg_excerpt(Profiles_MessageHelper::formatBodyExcerpt($message->bodyExcerpt), Profiles_MessageHelper::MAX_BODY_EXCERPT_DISPLAY_LENGTH)) %>
            </span>
        </a>
    </td>
</tr>
