<?php
/**
 * Useful functions for working with the profile-questions form
 *
 * @see Profiles_ProfileQuestionFormHelperTest
 */
class Profiles_ProfileQuestionFormHelper {

    /**
     * Validates the submitted profile form to ensure that required questions are filled out.
     *
     * @return array  HTML error messages, optionally keyed by field name
     */
    public static function validateForm() {
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_ProfileQuestionHelper.php');
        $questions = Profiles_ProfileQuestionHelper::getQuestions(W_Cache::getWidget('profiles'));
        $errors = array();
        foreach ($questions as $question) {
            $questionName = 'question_' . $question['questionCounter'];
            if (!$question['required']) {
                continue;
            }

            if ($question['answer_type'] == 'select' && $question['answer_multiple']) {
                if (!isset($_POST[$questionName]) || !is_array($_POST[$questionName])) {
                    $_POST[$questionName] = array();
                }
                $choices = array_map('trim', explode(',', $question['answer_choices']));
                $cnt = 0;
                foreach($_POST[$questionName] as $answer) {
                    if (in_array($answer, $choices)) {
                        $cnt++;
                    }
                }
                if (!$cnt) {
                    $errors[$questionName] = xg_html('PLEASE_SELECT_OPTION_FOR_X', $question['question_title']);
                }
            } else if ($question['answer_type'] == 'date') {
                if ( !checkdate($_POST[$questionName."_month"], $_POST[$questionName."_day"], $_POST[$questionName."_year"]) ) {
                    $errors[$questionName] = xg_html('PLEASE_PROVIDE_DATE_FOR_X', $question['question_title']);
                }
            } else if (!mb_strlen($_POST[$questionName])) {
                $errors[$questionName] = xg_html('PLEASE_ENTER_SOMETHING_FOR_X', $question['question_title']);
            }
        }
        return $errors;
    }

    /**
     * Populates the User object with the submitted values.
     *
     * Expected POST variables:
     *     TODO: document
     *
     * @param $user XN_Content|W_Content  the User object to update
     */
    public static function write($user) {
        // Legacy code [Jon Aquino 2007-09-13]
        $user->isPrivate = true; //BAZ-2327
        W_Cache::getWidget('profiles')->includeFileOnce('/lib/helpers/Profiles_ProfileQuestionHelper.php');
        foreach (Profiles_ProfileQuestionHelper::getQuestions(W_Cache::getWidget('profiles')) as $question) {
            $attrName = Profiles_ProfileQuestionHelper::attributeNameForQuestion($question, W_Cache::getWidget('profiles'));
            if ($question['answer_type'] == 'date') {
                $yearField = 'question_'.$question['questionCounter'].'_year';
                $monthField = 'question_'.$question['questionCounter'].'_month';
                $dayField = 'question_'.$question['questionCounter'].'_day';
                if (isset($_POST[$yearField]) && isset($_POST[$monthField]) && isset($_POST[$dayField]) &&
                ($year = $_POST[$yearField]) && ($month = $_POST[$monthField]) && ($day = $_POST[$dayField])) {
                    /* The submitted answers could be blank or invalid if the user has submitted the form with the
                     * "Save and Finish Later" button, so just save stuff that's valid (BAZ-1735) */
                    if (checkdate($month, $day, $year)) {
                        $user->my->set($attrName, sprintf('%04d-%02d-%02dT00:00:00Z', $year, $month, $day), XN_Attribute::DATE);
                    }
                } else {
                    $user->my->remove($attrName);
                }
            } else {
                if (isset($_POST['question_' . $question['questionCounter']])) {
                    $val = $_POST['question_' . $question['questionCounter']];
                    // Strip any initial http:// from URL fields
                    if (($question['answer_type'] == 'url') && (mb_substr($val,0,7) == 'http://')) {
                        $val = mb_substr($val,7);
                    }
                    if (($question['answer_type'] == 'text') || ($question['answer_type'] == 'textarea')) {
                        $val = xg_scrub($val);
                    }
                    if (($question['answer_type'] == 'select') && ($question['answer_multiple'] == 'on')) {
                        //  BAZ-2144: serialize all multiple choice answers
                        $val = serialize($val);
                    }
                    $user->my->{$attrName} = $val;
                } else {
                    $user->my->remove($attrName);
                }
            }
        }
    }

    /**
     * Reads the profile question answers from the specified user object
     *
     * @param $user XN_Content|W_Content the User object to read from
     */
    public static function read($user) {
        $profileWidget = W_Cache::getWidget('profiles');
        $profileWidget->includeFileOnce('/lib/helpers/Profiles_ProfileQuestionHelper.php');
        $questions = Profiles_ProfileQuestionHelper::getQuestions($profileWidget);
        $questionsAndAnswers = array();
        foreach ($questions as $question) {
            $answerArray = array();
            $answerArray['private'] = isset($question['private']) ? $question['private'] : false;
            $attrName = Profiles_ProfileQuestionHelper::attributeNameForQuestion($question,$profileWidget);
            $rawAnswer = $user->my->{$attrName};
            $safeQuestion = xnhtmlentities($question['question_title']);
            if ((is_array($rawAnswer) && count($rawAnswer)) || mb_strlen($rawAnswer)) {
                if ($question['answer_type'] == 'date') {
                    /* Use DateTime to handle broader year range (BAZ-4169). This is
                     * only OK here because we don't care about timezone -- just
                     * month / day / year */
                    $answer = date_format(new DateTime($rawAnswer),xg_text('F_J_Y'));
                } elseif ($question['answer_type'] == 'url') {
                    $answer = '<a href="http://' . xnhtmlentities($rawAnswer) . '">http://' . xnhtmlentities($rawAnswer) . '</a>';
                } else if (($question['answer_type'] == 'select')
                        && ($question['answer_multiple'] == 'on')
                        && ($unserial = @unserialize($rawAnswer))) {
                    //  Multiple choice answers should be serialized for BAZ-2144
                    //    Older ones might not be yet
                    $answer = xnhtmlentities(implode(', ', $unserial));
                } elseif (is_array($rawAnswer)) {
                    $answer = xnhtmlentities(implode(', ', $rawAnswer));
                } elseif (($question['answer_type'] == 'text') || ($question['answer_type'] == 'textarea')) {
                    // HTML that's been scrubbed on the way in
                    $answer = $rawAnswer;
                } else {
                    $answer = xnhtmlentities($rawAnswer);
                }
                $answerArray['answer'] = $answer;
                $questionsAndAnswers[$safeQuestion] = $answerArray;
            }
        }

        return array('questions' => $questions, 'answers' => $questionsAndAnswers);
    }

}
