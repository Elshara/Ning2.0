<?php
/**
*  Add a list of post-to-external-service links/buttons
*
* @param $widgetTitle   String  The title of the thing being shared
* @param $embedCode     String  The html code of the thing being shared
* @param $widgetId      String  The widget id to use with pre-stored widget templates on clearspring
* @param $config        String  The config object containing all the flashvars in JSON
* @param $appUrl        String  The network url
* @param $linkUrl       String  The page of the thing being shared
* @param $csWidgetName  String  The widget name identification on ClearSpring (http://www.clearspring.com/csmanager)
* @param $source        String  The id of the html element being shared
* @param $fbApp         Boolean If the widget should display an "add to facebook" to add an app instead of the "share on facebook" link
* @param $fbAppUrl      String  The url of the facebook app for that widget
* @param $disableOthers Boolean If the display of the clearspring button should be disabled
**/

$widgetTitle = addslashes($widgetTitle);
$embedCode = xnhtmlentities(addslashes($embedCode));

//Myspace size limit is 1000 chars http://x.myspace.com/download/posttomyspacedeveloperdocumentation.pdf
//Clearspace size limit is 2000 chars for the urlencoded blob
$limit = 2000;
$embedCodeTooBig = (mb_strlen(urlencode($embedCode)) > $limit);
if (mb_strlen(urlencode($embedCode.$widgetTitle)) > $limit) $widgetTitle = '';
if (mb_strlen(urlencode($embedCode.$widgetTitle.$appUrl)) > $limit) $appUrl = '';
?>
<ul class="services-vert">
<?php
if (!$embedCodeTooBig){ ?>
<li><a href="javascript:void(0);" onclick="xg.index.embeddable.list.postToMySpace('<%= $widgetTitle %>', '<%= $embedCode %>', '<%= $appUrl %>', 5)" class="desc service-myspace"><%= xg_html('ADD_TO_MYSPACE')%></a></li><?php
} ?>
<?php XG_App::includeFileOnce('/lib/XG_ShareHelper.php'); ?>
<li class="share-on-facebook" <%= $fbApp ? 'style="display:none;"' : '' %>><a class="desc service-facebook" target="_blank" onclick="return xg.index.embeddable.list.postToFacebook('<%= xnhtmlentities(XG_ShareHelper::postToFacebookUrl($linkUrl)) %>')" href="<%= xnhtmlentities(XG_ShareHelper::postToFacebookUrl($linkUrl)) %>"><%= xg_html('SHARE_ON_FACEBOOK')%></a></li>
<li class="add-to-facebook"  <%= $fbApp ? '' : 'style="display:none;"' %>><a class="desc service-facebook" target="_blank" href="<%= $fbAppUrl %>"><%= xg_html('FACEBOOK_ADD_TO_FACEBOOK')%></a></li><?php
if ((!$disableOthers) && (!$embedCodeTooBig)) {
     ?>
<li><a id="<%= $widgetId %>" class="desc service-other" href="javascript:void(0);"><%= xg_html('ADD_TO_OTHER')%></a></li>
<script type="text/javascript">
xg.addOnRequire(function() {
	// BAZ-9805: Moving csLaunchpadTarget out of here. [Andrey 2008-09-13]
	var div = dojo.byId("csLaunchpadTarget_<%=$widgetId%>");
	var a = dojo.byId("<%=$widgetId%>");
	div.parentNode.removeChild(div);
	document.getElementById('xg_body').appendChild(div);
	var o = xg.shared.util.getOffset(a, div);
	div.style.left = o.x + 'px';
	div.style.top = o.y + a.offsetHeight + 'px';
});
this['menu_<%= $widgetId %>']  = $Launchpad.CreateMenu({
  actionElement: '<%= $widgetId %>',
  targetElement: "csLaunchpadTarget_<%= $widgetId %>",
  servicesInclude: [
    "blogger",
    "friendster",
    "google",
    "freewebs",
    "live",
    "livejournal",
    "piczo",
    "netvibes",
    "tagged",
    "typepad",
    "vox",
    "xanga",
    "pageflakes",
    "myyearbook",
    "perfspot"
  ],
  customCSS: "<%= Index_ClearspringHelper::getClearspringCssUrl() %>"<%=
  ($csWidgetName) ? (', widgetName: "' . $csWidgetName .'"') : ('') %><%=
  ($source) ? (', source: "' . $source .'"') : ('') %><%=
  ($widgetId) ? (', wid: "' . $widgetId .'"') : ('') %><%=
  ($config) ? (', config: ' . $config) : ('') %>
});
</script>
<div id="csLaunchpadTarget_<%= $widgetId %>" style="position:absolute; z-index:10;"></div>
<?php
}
?>
</ul>
