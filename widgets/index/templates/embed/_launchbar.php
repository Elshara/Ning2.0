<script type="text/javascript">
    if (typeof(xg_handleLaunchBarSubmit) == 'undefined') {
        //  Default implementation just goes to the url without
        //    submitting anything
        //
        //  Forms in the GYO sequence should override this function
        function xg_handleLaunchBarSubmit(url) {
            if (url) {
                window.location = url;
            }
        }
    }
</script>
<div id="xg_setup">
    <ul class="setup_progress">
        <?php
        $numSteps = count($this->prelaunchSteps) - 1;
        $n = 0;
        foreach ($this->prelaunchSteps as $step) {
            $link = $this->_widget->buildUrl($step['controller'], $step['action']);
            echo '<li';
            $classes = array();
            if (0 == $n) { $classes[] = 'first-child'; }
            if ($this->requestedStep === $n) {
                $classes[] = 'this';
                $step['state'] = 'current';
            }
            if ($numSteps == $n) { $classes[] = 'last-child';	}
            if (count($classes) > 0) {
                echo " class='" . join(' ', $classes) . "'";
            }
            echo '>';
            echo '<a class="' . $step['state'] . '" id="xg_setup_button_' . $n . '"';
            echo ' href="javascript:void(0)">' . xnhtmlentities($step['displayName']) . '</a>';
            echo "</li>\n"; ?>
            <script type="text/javascript">xg.addOnRequire(function(){
            	dojo.event.connect(dojo.byId('xg_setup_button_<%= $n %>'), 'onclick', function(evt) {
                    xg_handleLaunchBarSubmit('<%= $link %>', evt);
                });
			})</script>
            <?php
            $n++;
        } ?>
    </ul>
</div>