<?php
/** Show the archive list in a sidebar module
 *
 * @param $title string  text for the module heading
 * @param $archive One of the archives returned by Profiles_BlogArchiveHelper::getPostArchive() (friends, me, all)
 * @param $user optional user name if this is embedded on a particular user's blog post or list. This is used in the
 *  archive link generation
 * @param $promoted boolean  true if the tags are for featured blog posts
 */ ?>
<div class="xg_module">
    <div class="xg_module_head">
        <h2><%= xg_text($title) %></h2>
    </div>
    <div class="xg_module_body">
        <?php
        if (isset($user)) {
            $baseUrlArgs = array('user' => $user);
        } else {
            $baseUrlArgs = array();
        }
        $monthNames= Profiles_BlogController::getMonths();
        if (is_array($archive)) {
            krsort($archive);
            foreach ($archive as $year => $months) {
                // Would $months ever not be an array? [Jon Aquino 2008-02-02]
                if (! is_array($months)) { continue; }
                if (! array_sum($months)) { continue; } // BAZ-4185 [Jon Aquino 2008-02-02]
                echo '<p><strong>'.$year.'</strong></p>';
                echo '<ul class="nobullets">';
                krsort($months);
                foreach ($months as $month => $count) {
                    if ($count > 0) {
                        echo '<li><a href="'.xnhtmlentities($this->_buildUrl('blog','list',array_merge($baseUrlArgs,array('month' => $month, 'year' => $year, 'promoted' => $promoted)))).'">'. $monthNames[intval($month)].'</a> ('.$count.')</li>';
                    }
                }
                echo '</ul>';
            }
        } ?>
    </div>
</div>
