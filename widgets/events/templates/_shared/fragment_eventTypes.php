<?php
/*  $Id: $
 *
 *  Displays popular event types
 *
 *  Parameters:
 *		$types
 *		$title
 *		$urlPrefix
 *		$viewAllUrl
 */
arsort($types,SORT_NUMERIC);
$ids = array();
?>
<div class="xg_module eventmodule">
    <div class="xg_module_head">
        <h2><%=xnhtmlentities($title)%></h2>
    </div>
    <div class="xg_module_body">
        <ul class="nobullets">
            <?php $i = 0; foreach ($types as $type=>$cnt) {
                echo '<li'.$html.'><a href="'.$urlPrefix.urlencode($type).'">'.xnhtmlentities($type).'</a> ('.$cnt.')</li>';
                if (++$i >= 5) {
                    break;
                }
            } ?>
        </ul>
        <?php if ($i>=5) { ?>
            <p class="right"><small><a href="<%=$viewAllUrl%>"><%=xg_html('VIEW_ALL')%></a></small></p>
        <?php } ?>
    </div>
</div>