<div style="padding: 0pt 5px; margin-left: 9px;" class="xg_column xg_span-4 last-child">
    <h3><%= $title %></h3>
    <ul class="videothumbs">
        <?php foreach($videos as $video) { 
            $duration = Video::getDuration($video);
            $videoUrl = W_Cache::getWidget('video')->buildUrl('video', 'show', array('id' => $video->id));
        ?>
            <li>
                <a href="<%= $videoUrl %>" class="xg_column xg_span-2"><img alt="" width="89" src="<%= xnhtmlentities(Video_VideoHelper::thumbnailUrl($video,89)) %>"/></a>
                <span class="xg_column xg_span-2 last-child">
                    <a href="<%= $videoUrl %>"><%= xnhtmlentities(xg_excerpt($video->title, 50)) %></a><br/>
                    <%= $duration ? '<small>' . $duration . '</small>' : '' %>
                </span>
            </li> 
        <?php } ?>
    </ul>
</div>