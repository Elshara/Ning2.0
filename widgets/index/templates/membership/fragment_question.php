<?php
/* Partial template to display one question input on the "Add/Edit Profile Questions" form
 *
 * @param $counter integer question number, used in element names and IDs for disambiguation
 * @param $question array optional array containing default values for title, type, required, choices, multiple
 *		The structure of this array mirrors the form element names and its values override
 *		anything specified in $title, $type, $required, $choices, $multiple
 * @param $title string optional default value to use for question title
 * @param $type string optional default value for the question type <select/> menu
 * @param $required boolean optional default value for the "Required?" check box
 * @param $choices string optional default value for the Choices text box (used only for Multiple Choice questions)
 * @param $multiple boolean optional default value for whether multiple choices are allowed
 */

$counter = isset($counter) ? (integer) $counter : 1;
if (isset($question)) {
    if (isset($question['question_title'])) { $title = $question['question_title']; }
    if (isset($question['answer_type'])) { $type = $question['answer_type']; }
    if (isset($question['required'])) { $required = $question['required']; }
    if (isset($question['private'])) { $private = $question['private']; }
    if (isset($question['answer_choices'])) { $choices = $question['answer_choices']; }
    if (isset($question['answer_multiple'])) { $multiple = $question['answer_multiple']; }
    if (isset($question['position'])) { $position = $question['position']; }
    if (isset($question['questionCounter'])) { $questionCounter = $question['questionCounter']; }
}

$title	 = isset($title) ? $title : '';
if (! (isset($type) && isset(Index_MembershipController::$profileQuestionTypes[$type]))) {
    $tmp = array_keys(Index_MembershipController::$profileQuestionTypes);
    $type = $tmp[0];
}
$required   = isset($required) ? (boolean) $required : false;
$private    = isset($private) ? (boolean) $private : false;
$choices    = isset($choices) ? $choices : '';
$multiple   = isset($multiple) ? (boolean) $multiple : false;
$position   = isset($position) ? (integer) $position : 0;

?>
<fieldset class="fieldset move" id="question_<%= $counter %>">
    <dl>
        <dt><label for="question_title_<%= $counter %>"><%= xg_html('QUESTION_TITLE') %></label></dt>
        <dd><input name="question_title_<%= $counter %>" id="question_title_<%= $counter %>" type="text" class="textfield" value="<%= xnhtmlentities($title) %>" /></dd>
        <dt><label for="answer_type_<%= $counter %>"><%= xg_html('ANSWER_TYPE') %></label></dt>
        <dd>
            <input type="hidden" name="position_<%= $counter %>" id="position_<%= $counter %>" value="<%= $position %>">
            <input type="hidden" name="questionCounter_<%= $counter %>" id="questionCounter_<%= $counter %>" value="<%= $questionCounter %>">
            <select name="answer_type_<%= $counter %>" id="answer_type_<%= $counter %>">
            <?php foreach (Index_MembershipController::$profileQuestionTypes as $questionType => $questionTypeLabel) { ?>
                <option value="<%= xnhtmlentities($questionType)%>" <%= ($type == $questionType) ? 'selected="selected"' : '' %>><%= xnhtmlentities($questionTypeLabel) %></option>
            <?php } ?>
            </select>
            <label for="required_<%= $counter %>"><input name="required_<%= $counter %>" id="required_<%= $counter %>" type="checkbox" class="checkbox" <%= $required ? 'checked="checked"' : '' %>/><%= xg_html('REQUIRED') %></label>
            <label class="private" for="private_<%= $counter %>">
                <input name="private_<%= $counter %>" id="private_<%= $counter %>" type="checkbox" class="checkbox" <%= $private ? 'checked="checked"' : '' %> _originallyChecked="<%= $private ? 'Y' : 'N' %>" />
                <%= xg_html('PRIVATE') %>
            </label>
        </dd>
    </dl>
    <dl id="choices_container_<%= $counter %>" <%= ($type != 'select') ? 'style="display:none"' : '' %>>
        <dt><%= xg_html('CHOICES') %></dt>
        <dd>
            <input name="answer_choices_<%= $counter %>" id="answer_choices_<%= $counter %>" type="text" class="textfield choices" value="<%= xnhtmlentities($choices) %>" />
            <label for="answer_multiple_<%= $counter %>"><input name="answer_multiple_<%= $counter %>" id="answer_multiple_<%= $counter %>" type="checkbox" class="checkbox" <%= $multiple ? 'checked="checked"' : '' %> /><%= xg_html('CAN_PICK_MORE') %></label><br />
            <small><%= xg_html('SEPARATE_EACH_CHOICE') %></small>
        </dd>
    </dl>
    <ul class="actions">
        <li><a href="#" id="remove_<%= $counter %>" class="delete desc"><%= xg_html('REMOVE') %></a></li>
        <li><a href="#" id="add_<%= $counter %>" class="add desc"><%= xg_html('ADD_ANOTHER_QUESTION') %></a></li>
    </ul>
</fieldset>
