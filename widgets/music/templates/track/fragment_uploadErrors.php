<?php
/**
 * Renders the error messages when the uploading of tracks failed.
 *
 * @param $sizeLimitError  If the upload size limit was exceeded during the previous upload
 * @param $failedFiles  The file names of the files where the upload failed
 * @param $allHadErrors  Whether the upload failed for all uploaded files
 * @param $id
 */
if ($sizeLimitError) { ?>
    <dl class="errordesc msg clear" id="<%= $id %>">
        <dt><%= xg_html('A_PROBLEM_OCCURRED') %></dt>
        <?php
        if ($sizeLimitError) { ?>
            <dd><ol><li><%= xg_html('TRACKS_EXCEEDED_LIMIT') %></li></ol></dd>
        <?php
        } ?>
    </dl>
<?php
} elseif ($failedFiles) { ?>
    <dl class="errordesc msg clear" id="<%= $id %>">
        <dt><%= xg_html('PROBLEM_UPLOADING_FILES', count($failedFiles)) %></dt>
        <dd>
            <ol>
                <?php
                foreach (explode(',', $failedFiles) as $filename) { ?>
                    <?php if ($filename) { ?>
                        <li><%= xnhtmlentities($filename) %></li>
                    <?php } ?>
                <?php
                } ?>
            </ol>
        </dd>
    </dl>
<?php
} else { ?>
    <dl class="errordesc msg" id="<%= $id %>" style="display: none"></dl>
<?php
} ?>
