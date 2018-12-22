<?php xg_header('manage',xg_text('PROFILE_QUESTIONS'),null, array('forceDojo'=>true)); ?>
<?php XG_App::ningLoaderRequire('xg.index.membership.questions'); ?>
<script type="text/javascript">
    xg.addOnRequire(function() {
        xg_handleLaunchBarSubmit = xg.index.membership.questions.handleLaunchBarSubmit;
    });
</script>
<div id="xg_body">
    <div class="xg_colgroup">
        <div class="xg_3col first-child">
			<%= xg_headline(xg_text('PROFILE_QUESTIONS'))%>
			<?php $this->renderPartial('fragment_success', 'admin'); ?>
            <div class="xg_module">
                <form id="questions_form" method="post" action="<%= xnhtmlentities($this->_buildUrl('membership','questions')) %>">
                    <%= XG_SecurityHelper::csrfTokenHiddenInput() %>
                    <input type="hidden" name="successTarget"/>
                    <div class="xg_module_body">
                        <?php
                        if ($this->showNotification) {
                            echo "<dl class='" . $this->notificationClass . " msg' id='questions_form_notify'>\n";
                            if ($this->notificationTitle) {
                                echo "<dt>" . xnhtmlentities($this->notificationTitle) . "</dt>\n";
                            }
                            echo "<dd><p>" . xnhtmlentities($this->notificationMessage) . "</p></dd>\n";
                            echo "</dl>\n";
                        } else {
                            echo "<dl class='errordesc msg' id='questions_form_notify' style='display: none'></dl>\n";
                        } ?>
                        <p><%= xg_html('WHAT_QUESTIONS_WOULD_YOU_LIKE') %></p>
                        <h3><%= xg_html('DEFAULT_QUESTIONS') %></h3>
                        <p><%= xg_html('YOU_CAN_CHOOSE_TO_KEEP') %></p>
                        <ul class="nobullets">
                            <li><label><%= $this->form->checkbox('showGenderFieldOnCreateProfilePage', 1, 'class="checkbox"') %><%= xg_html('GENDER') %></label></li>
                            <li><label><%= $this->form->checkbox('showLocationFieldOnCreateProfilePage', 1, 'class="checkbox"') %><%= xg_html('CITY_AND_STATE') %></label></li>
                            <li><label><%= $this->form->checkbox('showCountryFieldOnCreateProfilePage', 1, 'class="checkbox"') %><%= xg_html('COUNTRY') %></label></li>
                        </ul>
                    </div>
                    <div id="xg_membership_questions_div" class="xg_module_body<%= $this->initialQuestionCount == 0 ? ' hidden' : '' %>">
                        <h3><%= xg_html('CUSTOM_QUESTIONS') %></h3>
                        <p><%= xg_html('TO_REORDER_THE_QUESTIONS') %></p>
                        <div id="xg_membership_question_container">
                            <?php
                            if ($this->initialQuestionCount > 0) {
                                $questionRange = range(0, $this->initialQuestionCount - 1);
                                foreach($questionRange as $c) {
                                    $question = isset($this->profileQuestions[$c]) ? $this->profileQuestions[$c] : null;
                                    $this->renderPartial('fragment_question', array('counter' => $c, 'question' => $question));
                                }
                            } ?>
                            <script type="text/javascript">
                                xg.addOnRequire(function() {
                                    xg.index.membership.questions.activateQuestions([<%= implode(',',$questionRange) %>]);
                                });
                            </script>
                        </div>
                        <?php if ($this->displayPrelaunchButtons) {
                            $this->renderPartial('_backnext', 'embed');
                        } else { ?>
                            <p class="buttongroup">
                                <input type="button" class="button button-primary" onClick='xg.index.membership.questions.submitForm()' value="<%= qh(xg_html('SAVE')) %>" />
                                <a class="button" href="<%= $this->_widget->buildUrl('admin', 'manage') %>"><%= xg_html('CANCEL')%></a>
                            </p>
                        <?php } ?>
                    </div>
					<div id="xg_membership_no_questions_div" class="xg_module_body<%= $this->initialQuestionCount > 0 ? ' hidden' : '' %>">
                        <h3><%= xg_html('CUSTOM_QUESTIONS') %></h3>
                        <p><%= xg_html('YOU_HAVE_NO_PROFILE_QUESTIONS') %></p>
                        <p><input type="button" class="button" onClick="xg.index.membership.questions.addFirstQuestion();" value="<%= qh(xg_html('ADD_A_QUESTION')) %>"></p>
                    </div>
                </form>
            </div>
        </div>
        <div class="xg_1col last-child">
            <?php xg_sidebar($this) ?>
        </div>
    </div>
</div>

<?php xg_footer(); ?>
