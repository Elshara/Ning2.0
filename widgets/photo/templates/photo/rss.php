<?php
// @param photos
// @param title
// @param link

// pubDate and lastBuild date are required by some aggregators, like Bloglines.
// See http://jonaquino.blogspot.com/2005/02/rolling-your-own-rss-feed-be-sure-to.html
// [Jon Aquino 2005-11-09]
$pubDate = (date('r'));
if(($_GET['mini'])&&(!$_GET['photo_width'])){
    //sidebar
    $photoWidth = 204;
    $photoHeight = 153;
} else if (($_GET['small'])&&(!$_GET['photo_width'])){
    //embed, other websites
    $photoWidth = 441;
    $photoHeight = 330;
} else {
    $photoWidth = ($_GET['photo_width'])?$_GET['photo_width']:800;
    $photoHeight = ($_GET['photo_height'])?$_GET['photo_height']:600;
}
$thumbWidth = 80;
$thumbHeight = 80;

echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<rss version="2.0"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:georss="http://www.w3.org/2003/01/geo/wgs84_pos#"
    >
    <channel>
        <title><%= $this->title %></title>
        <description><%= xnhtmlentities($this->description) %></description>
        <link><%=$this->link;%></link>
        <pubDate><%=$pubDate;%></pubDate>
        <lastBuildDate><%=$pubDate;%></lastBuildDate>
        <image>
            <title><%=$this->title%></title>
            <link><%=$this->link;%></link>
            <url><%=xnhtmlentities($this->feedImageUrl);%></url>
            <height><%=$this->feedImageHeight;%></height>
        </image>
        <?php
    XG_App::includeFileOnce('/lib/XG_TagHelper.php');
    foreach ($this->photos as $photo) {
            Photo_HtmlHelper::fitImageIntoThumb($photo, $photoWidth, $photoHeight, $imgUrl, $imgWidth, $imgHeight);
            Photo_HtmlHelper::fitImageIntoThumb($photo, $thumbWidth, $thumbHeight, $thumbUrl, $imgThumbWidth, $imgThumbHeight);
            $allowedTypes = array(
                'image/jpeg',
                'image/x-jpeg',
                'application/jpeg',
                'application/x-jpeg',
                'image/png',
                'image/x-png',
                'application/png',
                'application/x-png',
                'image/gif',
                'image/x-gif',
                'application/gif',
                'application/x-gif'
                 );
            if(!in_array($photo->my->mimeType, $allowedTypes)){
                $photo->my->mimeType = 'image/jpeg';
                $imgUrl = Photo_HtmlHelper::addParamToUrl($imgUrl, 'format', 'jpg');
                $thumbUrl = Photo_HtmlHelper::addParamToUrl($thumbUrl, 'format', 'jpg');
            }
            $popularTags = $this->useTags ? XG_TagHelper::getTagNamesForObject($photo->id, 6) : array();
            ob_start(); ?>
            <a href="<?php echo $this->_buildUrl('photo', 'show') . '?id=' . $photo->id ?>">
                <img src="<?php echo xnhtmlentities($imgUrl) ?>" alt="<%=$photo->title %>" />
            </a><br /><small><%= xg_html('BY_X', '<a href="' . $this->_buildUrl('photo', 'listForContributor', '?screenName='.$photo->contributorName) . '">' . xnhtmlentities(Photo_FullNameHelper::fullName($photo->contributorName)) . '</a>') %></small><br />
            <?php
            $thumbnail = trim(ob_get_contents()) . '<br />';
            ob_end_clean();
            $photodescription = $thumbnail . xg_nl2br($photo->description);
            $photolink = $this->_buildUrl('photo', 'show', '?id=' . $photo->id);
            $mimeType = xnhtmlentities($photo->my->mimeType);
            $imgUrl = xnhtmlentities($imgUrl);
      ?><item>
            <guid>http://<%= $_SERVER['HTTP_HOST'] %>/xn/detail/<%=$photo->id;%></guid>
            <title><%= xg_xmlentities($photo->title) %></title>
            <link><%=$photolink;%></link>
            <description><%=xg_xmlentities($photodescription);%></description>
            <pubDate><%=date('r',strtotime($photo->createdDate));%></pubDate>
            <enclosure url="<%=$imgUrl;%>" type="<%=$mimeType;%>" length="<%=($photo->my->length)?$photo->my->length:'1';%>" />
            <media:content url="<%=$imgUrl;%>" type="<%=$mimeType;%>" height="" width=""/>
            <media:description><%=xg_xmlentities($photo->description);%></media:description>
            <media:thumbnail url="<%=xnhtmlentities($thumbUrl);%>" width="<%=$imgThumbWidth;%>" height="<%=$imgThumbHeight;%>" />
            <media:keywords><%=xg_xmlentities(implode($popularTags, ', '));%></media:keywords>
            <media:credit role="photographer"><%= xg_xmlentities(Photo_FullNameHelper::fullName($photo->contributorName)); %></media:credit>
            <?php
            if (($photo->my->lat)&&($photo->my->lng)){ ?>
            <georss:point><%=$photo->my->lat;%> <%=$photo->my->lng;%></georss:point><?php
            }?>
        </item>
        <?php
    } ?>
    </channel>
</rss>
