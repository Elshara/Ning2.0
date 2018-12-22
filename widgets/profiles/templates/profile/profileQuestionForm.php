<?php /* Legacy code [Jon Aquino 2007-09-13] */ ?>
<?php XG_App::ningLoaderRequire('xg.profiles.profile.profileQuestionForm', 'xg.index.util.FormHelper'); ?>
<?php
$validations = array();
foreach ($this->questions as $question) {
    $questionFieldName = 'question_' . $question['questionCounter'];
    $privateQuestion = $question['private'] ? '<small class="private">' . xg_html('PRIVATE') . '</small>' : '';
    $safeTitle = xnhtmlentities($question['question_title']) . ($question['required'] ? ' *' : '');
    if ($this->onlyShowRequired && !$question['required']) {
        continue;
    }
    if ($question['answer_type'] == 'text') { ?>
        <p>
            <label for="<%= $questionFieldName %>"><%= $safeTitle %></label><%= $privateQuestion %><br />
            <%= $this->form->text($questionFieldName,'class="textfield" id="'.$questionFieldName.'"') %>
            <?php if ($question['required']) { $validations[] = array('validateRequired',$questionFieldName,xg_html('PLEASE_ENTER_SOMETHING_FOR_X', $question['question_title'])); } ?>
        </p>
    <?php
    } elseif ($question['answer_type'] == 'textarea') { ?>
        <p>
            <label for="<%= $questionFieldName %>"><%= $safeTitle %></label><%= $privateQuestion %><br />
            <%= $this->form->textarea($questionFieldName,'class="textarea" rows="5" id="'.$questionFieldName.'"') %>
            <?php if ($question['required']) { $validations[] = array('validateRequired',$questionFieldName,xg_html('PLEASE_ENTER_SOMETHING_FOR_X', $question['question_title'])); } ?>
        </p>
    <?php
    } elseif ($question['answer_type'] == 'select') {
        $inputTypeAndClass = $question['answer_multiple'] ? 'checkbox' : 'radio';
        $choices = explode(',',$question['answer_choices']); ?>
        <p class="label"><label><%= $safeTitle%><%= $privateQuestion %></label></p>
        <fieldset>
        <?php if ($question['answer_multiple']) { ?>
            <ul class="options">
                <?php
                if ($question['answer_multiple']) { $questionNameSuffix = '[]'; } else { $questionNameSuffix = ''; }
                foreach ($choices as $i => $choice) {
                    $optionFieldName = $questionFieldName . "_option$i";
                    $choices[$i] = $choice = trim($choice); ?>
                    <li><label for="<%= $optionFieldName %>"><%= $this->form->{$inputTypeAndClass}($questionFieldName.$questionNameSuffix,$choice,'class="'.$inputTypeAndClass.'" id="'.$optionFieldName.'"') %><%= xnhtmlentities($choice) %></label></li>
                <?php
                } ?>
            </ul>
        <?php } else {
            $singleSelectChoices = array_combine($choices, $choices);
        ?>
            <%= $this->form->select($questionFieldName, $singleSelectChoices); %>
        <?php } ?>
        </fieldset>
        <?php
        if ($question['required']) { $validations[] = array('validateRequired',$questionFieldName.$questionNameSuffix,xg_html('PLEASE_SELECT_OPTION_FOR_X', $question['question_title'])); }
    } elseif ($question['answer_type'] == 'url') { ?>
        <p>
            <label for="<%= $questionFieldName %>"><%= $safeTitle %></label><%= $privateQuestion %><br />
            <small>http://</small>&#160;<%= $this->form->text($questionFieldName,'class="textfield url" id="'.$questionFieldName.'"') %>
            <?php if ($question['required']) { $validations[] = array('validateRequired',$questionFieldName,xg_html('PLEASE_ENTER_SOMETHING_FOR_X', $question['question_title'])); } ?>
        </p>
    <?php
    } elseif ($question['answer_type'] == 'date') { ?>
        <p>
            <label for="<%= $questionFieldName %>_month"><%= $safeTitle %></label><%= $privateQuestion %><br />
            <%= $this->form->select($questionFieldName.'_month', $this->months,false,'class="select date" id="'.$questionFieldName.'_month"'); %>
            <%= $this->form->text($questionFieldName.'_day', 'class="textfield date" size="2" maxlength="2"'); %>
            <%= $this->form->text($questionFieldName.'_year', 'class="textfield date" size="4" maxlength="4"'); %>
        </p>
        <?php
        if ($question['required']) {
            $validations[] = array('validateRequiredDate', $questionFieldName, xg_html('PLEASE_PROVIDE_DATE_FOR_X', $question['question_title']), xg_html('PLEASE_PROVIDE_VALID_DATE_FOR_X', $question['question_title']));
        } else {
            $validations[] = array('validateDate', $questionFieldName, xg_html('PLEASE_PROVIDE_VALID_DATE_FOR_X', $question['question_title']));
        } ?>
    <?php
    } ?>
<?php
} /* each question */ ?>
<?php /* Emit the javascript for client-side validation */
if (count($validations)) { ?>
    <script type="text/javascript">
    xg.addOnRequire(function() {
        <?php
        $j = new NF_JSON();
        foreach ($validations as $validation) {
            $func = 'xg.index.util.FormHelper.'.array_shift($validation); ?>
            xg.profiles.profile.profileQuestionForm.addValidation(<%= $func %>, <%= $j->encode($validation) %>);
        <?php
        } /* each validation */ ?>
    });</script>
<?php
} /* validations? */?>
