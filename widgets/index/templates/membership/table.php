<?php
XG_App::ningLoaderRequire('xg.index.membership.list');
XG_App::includeFileOnce('/lib/XG_Message.php');

/* Adjust column widths based on number of columns; currently there's just a single extra column that can be passed */
if (!$this->showEmails && count($this->extraColumns) == 0) {
    $columnWidths = array('name' => '40%', 'status' => '40%', 'date' => '15%');
} elseif (!$this->showEmails) {
    $columnWidths = array('name' => '30%', 'status' => '30%', 'date' => '15%');
} elseif (count($this->extraColumns) == 0) {
    $columnWidths = array('name' => '25%', 'email' => '30%', 'status' => '25%', 'date' => '15%');
} else {
    $columnWidths = array('name' => '20%', 'email' => '25%', 'status' => '20%', 'date' => '15%');
}

if (count($this->extraColumns)) {
    $extraColumns = array_keys($this->extraColumns);
    $columnWidths[$extraColumns[0]] = '15%';
}


$nameSortUrl = XG_HttpHelper::addParameter(XG_HttpHelper::currentUrl(), 'sort', ($_GET['sort'] === 'name_a' ? 'name_d' : 'name_a'));
$statusSortUrl = XG_HttpHelper::addParameter(XG_HttpHelper::currentUrl(), 'sort', ($_GET['sort'] === 'status_a' ? 'status_d' : 'status_a'));
$dateSortUrl = XG_HttpHelper::addParameter(XG_HttpHelper::currentUrl(), 'sort', ($_GET['sort'] === 'date_a' || $_GET['sort'] == '' ? 'date_d' : 'date_a'));
$sortable = ($this->dateTitle !== xg_text('DATE_INVITED') && $this->dateTitle !== xg_text('DATE_REQUESTED'));
$statusSortable = (isset($this->statusSortable) ? $this->statusSortable : true);
$nameHeader = ($sortable ? '<a href="' . $nameSortUrl . '">' . xg_html('NAME') . '</a>' : xg_html('NAME'));
$statusHeader = ($sortable && $statusSortable ? '<a href="' . $statusSortUrl . '">' . xg_html('STATUS') . '</a>' : xg_html('STATUS'));
$dateHeader = ($sortable ? '<a href="' . $dateSortUrl . '">' . xnhtmlentities($this->dateTitle) . '</a>' : xnhtmlentities($this->dateTitle));

?>
<table class="members">
    <thead>
        <tr><?php
             /* IE7 doesn't fire onChange when a checkbox changes - it does fire onClick */ ?>
            <th width="20px"><input type="checkbox" onClick="xg.index.membership.list.setCheckboxes(dojo.byId('xg_member_form'), this.checked)" /></th>
            <th width="20px">&nbsp;</th>
            <th width="<%= $columnWidths['name'] %>"><%= $nameHeader %></th>
            <?php if ($this->showEmails) { ?>
                <th width="<%= $columnWidths['email'] %>"><%= xg_html('EMAIL') %></th>
            <?php } ?>
            <th width="<%= $columnWidths['status'] %>"><%= $statusHeader %></th>
            <?php if ($this->extraColumns['viewProfile']) { ?>
                <th width="<%= $columnWidths['viewProfile'] %>"><%= xg_html('PROFILE') %></th>
            <?php } ?>
            <th width="<%= $columnWidths['date'] %>"><%= $dateHeader %></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($this->users as $i => $user) {
            $profile = $user['ningId'] ? XG_Cache::profiles($user['ningId']) : ($user['email'] ? XG_Cache::profiles($user['email']) : '');
            $checkboxValue = $user['checkboxValue'] ? $user['checkboxValue'] : 'on'; ?>
            <tr<%= $i % 2 ? ' class="alt"' : '' %>>
                <td><%= $user['checkboxName'] ? '<input type="checkbox" name="' . $user['checkboxName'] . '" value="' . $checkboxValue . '" />' : '' %></td>
                <td>
                    <?php
                    if ($profile && $user['profileUrl']) {
                        echo xg_avatar($profile, 14);
                    } elseif ($profile) { ?>
                        <img class="photo" src="<%= XG_UserHelper::getThumbnailUrl($profile, 14, 14) %>" height="14" width="14" />
                    <?php
                    } else { ?>
                        <img class="photo" src="http://api.ning.com/icons/profile/-1?default=-1&size=14" height="14" width="14"/>
                    <?php
                    } ?>
                </td>
                <td>
                    <strong>
                        <?php
                        $name = $profile ? XG_UserHelper::getFullName($profile) : $user['name'];
                        // $fullName may be null for old Ning profiles [Jon Aquino 2008-01-03]
                        if (mb_strlen($name) == 0) { $name = $profile ? $profile->screenName : $name; }
                        if ($user['profileUrl']) { ?>
                            <a href="<%= xnhtmlentities($user['profileUrl']) %>"><%= xnhtmlentities($name) %></a>
                        <?php
                        } else { ?>
                            <%= xnhtmlentities($name) %>
                        <?php
                        } ?>
                    </strong>
                </td>
                <?php if($this->showEmails) { ?>
                    <td><%= xnhtmlentities($profile && $profile->email ? $profile->email : (XG_Message::isPseudoEmailAddress($user['email']) ? '' : $user['email'])) %></td>
                <?php } ?>
                <%= $user['statusHtml'] %>
                <?php if ($this->extraColumns['viewProfile']) { ?><td>
                    <?php if ($user['viewProfileUrl']) { ?>
                        <a href="<%= xnhtmlentities($user['viewProfileUrl']) %>"><%= xg_text('VIEW_PAGE') %></a>
                    <?php } else { ?>
                        <%= xg_html('NO_PAGE_YET') %>
                    <?php } ?>
                </td><?php } ?>
                <td><%= xg_date(xg_text('M_J_Y'), $user['date']) %></td>
            </tr>
        <?php
        } ?>
    </tbody>
</table>
<?php if ((! $this->users) && (isset($_GET['q']))) { ?>
    <p class="results"><%= xg_html('SORRY_NO_MEMBERS_MATCHING_X_WERE_FOUND', xnhtmlentities($_GET['q'])); %></p>
<?php } ?>
