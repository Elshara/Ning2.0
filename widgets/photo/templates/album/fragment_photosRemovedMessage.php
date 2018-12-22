<?php
/**
 * @param $numPhotos
 * @param $album
 * @param $editForm
 */
if ($numPhotos) { ?>
    <div class="xg_module">
        <div class="xg_module_body">
            <h3><%= xg_html('OUR_APOLOGIES') %></h3>
            <p><%= xg_html('N_PHOTOS_FROM_ALBUM', xg_number($numPhotos)) %>
            <%= ($editForm)?xg_html('ADD_PHOTOS_TO_ALBUM'): xg_html('YOU_CAN_ADD_PHOTOS', 'href="'. xnhtmlentities($this->_buildUrl('album','edit','?id='.$album->id)).'"')%></p>
        </div>
    </div>
<?php
}