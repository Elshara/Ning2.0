<ul class="setup_nav<%= ((isset($dontClear) && $dontClear) ? '' : ' clear') %>">
    <?php
    // TODO: Eliminate the inline JavaScript [Jon Aquino 2008-04-11]
    $rnd = rand();
    if (isset($this->backLink)) { ?>
        <li class="prev" id="xg_setup_back_header">
            <a href="javascript:void(0)" id="xg_setup_back_header_a<%= $rnd %>"><%= xg_html('BACK') %></a>
        </li>
        <script type="text/javascript">
           	document.getElementById('xg_setup_back_header_a<%= $rnd %>').onclick = function(evt) {
				xg_handleLaunchBarSubmit('<%= $this->backLink %>', evt||window.event);
			};
        </script>
    <?php
    } ?>
    <li class="next" id="xg_setup_next_header">
        <?php W_Cache::getWidget('main')->includeFileOnce('/lib/helpers/Index_SetupHelper.php'); ?>
        <%= Index_SetupHelper::nextButton($this->nextLink, null, $this->nextText); %>
    </li>
</ul>