<div class="xg_body">
    <table width="100%">
        <tr>
            <td>
                <p><%= xg_html('YOU_CHANGED_YOUR_EMAIL_ON_X', $message['appName']) %></p>
                <p>
                    <%= xg_html('CLICK_HERE_TO_SIGN_IN_WITH_YOUR_NEW_EMAIL_ADDRESS') %><br />
                    <a href="<%= xnhtmlentities($signInUrl) %>"><%= xnhtmlentities($signInUrl) %></a>
                </p>
                <p>
                    <%= xg_html('IF_DID_NOT_CHANGE_EMAIL_ADDRESS') %><br />
                    <a href="<%= xnhtmlentities($contactUsUrl) %>"><%= xnhtmlentities($contactUsUrl) %></a>
                </p>
				 <p><%= xnhtmlentities($message['appName']) %></p>
            </td>
        </tr>
    </table>
</div>
