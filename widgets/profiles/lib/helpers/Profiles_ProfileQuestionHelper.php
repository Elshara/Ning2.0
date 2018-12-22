<?php

class Profiles_ProfileQuestionHelper {

    public static function getQuestions(W_Widget $w) {
        return unserialize(urldecode($w->config['profileQuestions']));
    }

    public static function putQuestions(W_Widget $w, $questions) {
        $sortedQuestions = array();
        foreach ($questions as $question) {
            $position = $question['position'] - 1;
            unset($question['position']);
            //  Don't overwrite a question - if there's a position conflict,
            //    move to the next available position
            while (isset($sortedQuestions[$position])) {
                $position++;
            }
            $sortedQuestions[$position] = $question;
        }
        ksort($sortedQuestions);
        $w->config['profileQuestions'] =
                urlencode(serialize(array_values($sortedQuestions)));
    }

    public static function prepareQuestionsFromSubmittedArray($ar) {
        $questions = array();
        foreach ($ar as $k => $v) {
            if (preg_match('@^(question_title|answer_type|required|private|answer_choices|answer_multiple|position|questionCounter)_(\d+)$@u', $k, $matches)) {
                $questionField = $matches[1];
                $questionNumber = $matches[2];
                if (! isset($questions[$questionNumber])) { $questions[$questionNumber] = array(); }
                $questions[$questionNumber][$questionField] = $v;
            }
        }
        // Remove blank questions
        foreach ($questions as $n => $fields) {
            if (! (isset($fields['question_title']) && mb_strlen(trim($fields['question_title'])))) {
                unset($questions[$n]);
            }
        }
        return array_values($questions);
    }

    public static function areQuestionsCompatible($q1, $q2) {
        //  Only date and non-date questions have incompatible answers!
        if ($q1['answer_type'] == 'date') {
            return ($q2['answer_type'] == 'date');
        }
        else {
            return ($q2['answer_type'] !== 'date');
        }
    }

    public static function incrementQuestionCounter(W_Widget $w) {
       return ($w->config['questionCounter'] = $w->config['questionCounter'] + 1);
    }

    public static function attributeNameForQuestion($question, $widget) {
        return XG_App::widgetAttributeName($widget, 'answer_q' . $question['questionCounter']);
    }

    /**
     * Make sure that the User shape is ready to save this question -- its type
     * in the shape should be acceptable
     *
     * @param $question array
     * @param W_Widget widget for attribute name construction
     * @param $minCounter integer  minimum value to use for the question counter
     */
    public static function updateUserShapeForQuestion($question, $widget, $minCounter) {
        $shape = XN_Shape::load('User');
        $attributeName = 'my.' . self::attributeNameForQuestion($question, $widget);
        $attributeNameBase = preg_replace('@\d+$@u','', $attributeName);
        $attributeNameRegex = '@^' . preg_quote($attributeNameBase,'@') . '(\d+)$@u';
        /* The attribute exists, make sure the type matches */
        /* IF
         * The attribute doesn't exist in the shape
         * OR
         * The shape attribute is "date", but the question is not
         * OR
         * The shape attribute is not date but the question is
         * THEN
         * we need to find a new counter for this question
         */
         // TODO: The date logic duplicates areQuestionsCompatible. Consolidate. [Jon Aquino 2008-04-05]
         // TODO: Try to eliminate the "if", as the caller already does a similar check. Test. [Jon Aquino 2008-04-05]
         if ((! isset($shape->attributes[$attributeName])) ||
             (($shape->attributes[$attributeName]->type == 'date') && ($question['answer_type'] != 'date')) ||
             (($shape->attributes[$attributeName]->type != 'date') && ($question['answer_type'] == 'date'))
             ) {
            $counter = $minCounter;
            foreach ($shape->attributes as $name => $attr) {
                if (preg_match($attributeNameRegex, $name, $matches) && ($matches[1] > $counter)) {
                    $counter = $matches[1];
                }
            }
            $attributeType = ($question['answer_type'] == 'date') ? XN_Attribute::DATE : XN_Attribute::STRING;
            /* The new counter should be one bigger */
            $counter++;
            $attempt = 0;
            $success = false;
            while ((! $success) && ($attempt < 5)) {
                try {
                    $attempt++;
                    $existingShapeVersion = $shape->version;
                    $shape->setAttribute($attributeNameBase . $counter, $attributeType);
                    /* create() throws an exception if the attribute already exists, so if
                     * it succeeds, we can be sure that this attribute creation didn't step
                     * on the toes of any simultaneous attribute creation */
                    $shape->attributes[$attributeNameBase.$counter]->create();
                    $success = true;
                } catch (Exception $e) {
                    /* If the shape save failed, then someone is already using this attribute with an
                     * incompatible type, so let's try a new attribute and try again */
                     $shape->deleteAttribute($attributeNameBase . $counter);
                     $counter++;
                }
            }
            if ($success) {
                return $counter;
            } else {
                throw new Exception("Couldn't update shape for profile question $attributeNameBase$counter = $attributeType");
            }
        }
        // TODO: Should we not return something if we get here? [Jon Aquino 2008-04-05]
    }

    /**
     * Returns the highest questionCounter in the given list of questions.
     *
     * @param $questions array  questions stored in profiles/widget-configuration.xml
     * @return integer  the max value for questionCounter
     */
    public static function maxCounter($questions) {
        $maxCounter = 0;
        foreach ($questions as $question) {
            $maxCounter = max($maxCounter, $question['questionCounter']);
        }
        return $maxCounter;
    }
}
