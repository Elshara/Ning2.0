<div id="add-<%= $this->prefix %>" class="add_section">
    <h3><%= xg_html('ADD_YOUR_FIRST_PHOTOS') %></h3>
    <img src="<%= xg_cdn(W_Cache::getWidget('main')->buildResourceUrl('gfx/features/photo.gif')) %>" alt="" class="feature_logo"/>
    <fieldset class="nolegend easyclear">
        <div class="upload">
            <input type="hidden" name="uploadMarker" value="present"/>
            <?php
            $this->renderPartial('fragment_uploadErrors', 'photo', array(
                    'id' => 'add_photos_form_notify', 'sizeLimitError' => $this->sizeLimitError, 'failedFiles' => $this->failedFiles, 'allHadErrors' => true)); ?>
            <p><label for="<%= $this->prefix %>_upload1"><%= xg_html('UPLOAD_PHOTOS_FROM') %></label></p>
            <ol>
                <li><input type="file" class="inputFile" id="<%= $this->prefix %>01" name="<%= $this->prefix %>01" /></li>
                <li><input type="file" class="inputFile" id="<%= $this->prefix %>02" name="<%= $this->prefix %>02" /></li>
                <li><input type="file" class="inputFile" id="<%= $this->prefix %>03" name="<%= $this->prefix %>03" /></li>
            </ol>
        </div>
        <p>
            <label for="<%= $this->prefix %>_title"><%= xg_html('ADD_TITLE_AND_DESCRIPTION_FOR_PHOTOS') %></label><br/>
            <input type="text" value="<%= $this->defaultTitle %>" name="<%= $this->prefix %>_title" class="textfield" id="<%= $this->prefix %>_title"/><br/>
            <textarea cols="45" rows="3" name="<%= $this->prefix %>_description" id="<%= $this->prefix %>_description"><%= $this->defaultDescription %></textarea>
        </p>
    </fieldset>
</div>
