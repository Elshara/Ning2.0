<?php

/**
 * Useful functions for working with the Get Your Own (network setup) flow.
 */
class  Index_SetupHelper {

    /**
     * The Next button, for the network setup flow.
     *
     * @param $nextLink  URL that the Next button should post to, or null if Next should launch the app
     * @param $launchLink boolean; whether to include the link to launch the network.  optional
     * @param $nextText string; the text for the Next link; defaults to xg_html('NEXT')
     */
    public static function nextButton($nextLink, $launchLink = false, $nextText = null) {
        if (!$nextText) {$nextText = xg_html('NEXT');}
        if ($launchLink) { ?>
            <%= xg_html('LAUNCH_OR', 'id="xg_setup_button_launch" href="javascript:void(0)"') %>
        <?php
        }
        $rnd = rand();
        if (isset($nextLink)) { ?>
            <a href="javascript:void(0)" id="xg_setup_next_header_a<%= $rnd %>" class="next"><%= $nextText %></a>
			<script type="text/javascript">
				document.getElementById('xg_setup_next_header_a<%= $rnd %>').onclick = function(evt) {
                    xg_handleLaunchBarSubmit('<%= $nextLink %>', evt||window.event);
                };
            </script>
        <?php
        } else { ?>
            <a href="javascript:void(0)" id="xg_setup_next_header_a<%= $rnd %>" class="next"><%= $nextText %></a>
            <script type="text/javascript">
                document.getElementById('xg_setup_next_header_a<%= $rnd %>').onclick = function(evt) {
                    xg_handleLaunchBarSubmit('<%= W_Cache::getWidget('main')->buildUrl('admin', 'launch') %>', evt||window.event);
				};
            </script>
        <?php
        }
        if ($launchLink) { ?>
        <script type="text/javascript">
            document.getElementById('xg_setup_button_launch').onclick = function(evt) {
				// in dojo it's not possible to disconnect an event tied to an anonymous function
				// the following prevents multiple click actions for BAZ-7213
				document.getElementById('xg_setup_button_launch').onclick = '';
                var launchUrl = '<%= W_Cache::getWidget('main')->buildUrl('admin', 'launch') %>';
                /* Compute timezone settings - BAZ-1628 */
                var now = new Date();
                var winter = new Date(now.getFullYear(), 0, 1, 12, 0, 0);
                var summer = new Date(now.getFullYear(), 6, 1, 12, 0, 0);
                var tzOffset = winter.getTimezoneOffset();
                var tzUseDST = (winter.getTimezoneOffset() == summer.getTimezoneOffset()) ? 0 : 1;
                // southern hemisphere
                if (winter.getTimezoneOffset() - summer.getTimezoneOffset() < 0) {
                    tzUseDST = -1;
                }
                launchUrl += '?tzOffset=' + tzOffset + '&tzUseDST=' + tzUseDST;
                xg_handleLaunchBarSubmit(launchUrl, evt||window.event);
            };
        </script>
        <?php }
    }

}
