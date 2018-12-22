<?php
/**
 * Displays the sub-navigation.
 *
 * @param $screenName string  username of the user that the current page is focused on
 * @param $moduleLinks array an array of the enabled modules in the network, links to the user page and a participation count
 * @param $currentLink string the name of the currently selected link.  Used for setting the appropriate class
 */
$myPage = XN_Profile::current()->screenName == $screenName;
?>
<ul class="navigation easyclear">
    <?php if ($myPage) { ?>
        <li <%= $currentLink == 'page' ? 'class="this"' : '' %>><a href="<%= xnhtmlentities(User::quickProfileUrl($screenName)) %>"><%= xg_html('MY_PAGE') %></a></li>
    <?php } else { ?>
        <li <%= $currentLink == 'page' ? 'class="this"' : '' %>><a href="<%= xnhtmlentities(User::quickProfileUrl($screenName)) %>"><%= xg_html('PAGE') %></a></li>    
    <?php } ?>
        <li <%= $currentLink == 'friends' ? 'class="this"' : '' %>><a href="<%= xnhtmlentities(User::quickFriendsUrl($screenName)) %>"><%= xg_html('FRIENDS') %></a></li>
    <?php
    foreach ($moduleLinks as $module) {
         $highlight = $module['name'] == $currentLink ? ' class="this"' : '';
         $linkAndTitle = '<li' .$highlight . '><a href="' . qh($module['url']) . '">' . qh($module['name']) . '</a></li>';
         if (!$myPage && (is_null($module['using']) || $module['using'] == 0)) {
             $linkAndTitle = '<li class="disabled">' . $module['name'] . '</li>';
         }
         ?>
         <%= $linkAndTitle %>
    <?php        
    } 
    if (XG_App::openSocialEnabled()) { ?>
        <li><a href="<%= xnhtmlentities(W_Cache::getWidget('opensocial')->buildUrl('application', 'apps', array('user' => $screenName))) %>"><%= xg_html('APPLICATIONS') %></a></li>  
    <?php
    }
     // preserve old style links for other pages using this fragment
        if (is_null($moduleLinks)) { ?>
            <li><a href="<%= xnhtmlentities($this->_buildUrl('blog','list',array('user' => $screenName))) %>"><%= xg_html('MY_BLOG') %></a></li>
     <?php
     }
    ?>
    <?php
    if ($currentLink == 'blog' && XN_Profile::current()->screenName == $screenName) { ?>
        <li class="right"><strong><a href="<%= xnhtmlentities($this->_buildUrl('blog', 'new')) %>" class="bigdesc add"><%= xg_html('ADD_NEW_BLOG_POST') %></a></strong></li>
    <?php    
    } elseif (XG_App::canSeeInviteLinks(XN_Profile::current())) { ?>
        <li class="right"><a href="<%= xnhtmlentities('/invite') %>" class="bigdesc add"><%= xg_html('INVITE_FRIENDS') %></a></li>
    <?php
    } ?>
</ul>
