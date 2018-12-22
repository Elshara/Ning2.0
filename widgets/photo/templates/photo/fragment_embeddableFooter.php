<?php
/**
 * Link back to the app. Appears after the embed code.
 */ ?>
<br /><small><a href="<%= xnhtmlentities($this->_widget->buildUrl('photo', 'index')) %>"><%= xg_html('FIND_MORE_PHOTOS_LIKE_THIS', preg_replace('/&#039;/u', "'", xnhtmlentities(XN_Application::load()->name))) %></a></small><br />
