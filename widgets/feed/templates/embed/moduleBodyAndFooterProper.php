<?php
$n = $this->feed->get_item_quantity($this->itemCount);
for ($i = 0; $i < $n; $i++) {
    $item = $this->feed->get_item($i); ?>
        <?php
        // Call html_entity_decode to work around SimplePie bug:
        // "Titles from RSS 2.0 feeds are escaped", http://simplepie.org/support/viewtopic.php?pid=2238
        // (BAZ-277) [Jon Aquino 2006-12-16]
        // Unescape it again, just in case (BAZ-2215) [Jon Aquino 2007-03-12]
        $title = html_entity_decode(html_entity_decode($item->get_title(), ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
        $url = html_entity_decode($item->get_permalink()); // BAZ-3903 [Jon Aquino 2007-08-16]
        if ($this->showDescriptions) { ?>
            <div class="xg_module_body">
                <h3>
                    <a href="<%= xnhtmlentities($url); %>"><%= xnhtmlentities($title); %></a>
                </h3>
                <%= xg_resize_embeds(xg_scrub($item->get_description()),$this->maxEmbedWidth); %>
            </div>
        <?php
        } else { 
        ?>
        <div class="xg_module_body">
            <p class="last-child"><a href="<%= xnhtmlentities($url); %>"><%= xnhtmlentities($title); %></a></p>
        </div>
        <?php
        } ?>
<?php
}?>
<div class="xg_module_foot">
    <ul>
        <?php if($this->feed->get_link()) { ?>
        <li class="right"><a href="<%= xnhtmlentities($this->feed->get_link()) %>"><%= xg_html('MORE_ELLIPSIS') %></a></li>
        <?php } ?>
    </ul>
</div>
<?php
xg_autodiscovery_link($this->feed->feed_url, $this->feed->get_title(), 'rss');
