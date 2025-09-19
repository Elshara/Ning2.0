<?php
$pubDate = strtotime($this->logItems[0]->createdDate);
echo '<?xml version="1.0" encoding="UTF-8" ?>';
?>
<rss version="2.0"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:georss="http://www.w3.org/2003/01/geo/wgs84_pos#"
    >
    <channel>
        <title><%= xg_xmlentities($this->rssTitle) %></title>
        <description><%= xg_xmlentities($this->description) %></description>
        <link><%=$this->link;%></link>
        <pubDate><%=date('r',$pubDate);%></pubDate>
        <lastBuildDate><%=date('r',$pubDate);%></lastBuildDate>
        <image>
            <title><%=xg_xmlentities($this->rssTitle)%></title>
            <link><%=$this->link;%></link>
            <url><%=xnhtmlentities($this->feedImageUrl);%></url>
            <height><%=$this->feedImageHeight;%></height>
        </image>
        <?php
    foreach($this->logItems as $item){
        ob_start();
        $this->renderPartial('fragment_logItem', 'log', array('item' => $item, 'isProfile' => false , 'fmt' => 'rss', ));
        $htmldescription = trim(ob_get_contents());
        ob_end_clean();
        $itemDate = strtotime($item->createdDate);
      ?><item>
            <guid isPermaLink="false"><%=$item->id;%></guid>
            <title><%= xg_xmlentities($item->title) %></title>
            <link><%= $item->my->link%></link>
            <description><%=xg_xmlentities($htmldescription);%></description>
            <pubDate><%=date('r',$itemDate);%></pubDate>
        </item>
        <?php
    } ?>
    </channel>
</rss>
