<?php
if ($this->embed->isOwnedByCurrentUser()) {
    XG_App::ningLoaderRequire('xg.shared.InPlaceEditor');
    ?>
    <h1 class="editable status" dojoType="InPlaceEditor" _control="textInput" _controlAttributes="<%= xnhtmlentities('class="textfield h1" size="50"') %>" _instruction=" " _maxLength="200" _setValueUrl="<%= $this->_buildUrl('profile', 'setPageTitle', '?screenName=' . xnhtmlentities($this->profile->screenName) . '&xn_out=json') %>"><%= xnhtmlentities($this->pageTitle) %></h1>
<?php
} else if (mb_strlen($this->pageTitle)) {?>
    <h1><%= xnhtmlentities($this->pageTitle) %></h1>
<?php
} ?>