<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/widgets/profiles/lib/helpers/Profiles_ProfileQuestionHelper.php');

class Profiles_ProfileQuestionHelperTest extends UnitTestCase {

    public function testMinCounter() {
        $questions = array (
            array (
              'question_title' => 'Relationship Status:',
              'questionCounter' => '30',
              'answer_type' => 'select',
              'answer_choices' => 'Single, In a Relationship, Engaged, Married, It\'s Complicated'),
            array (
              'question_title' => 'About Me:',
              'questionCounter' => '31',
              'answer_type' => 'textarea',
              'answer_choices' => ''),
            array (
              'question_title' => 'Website:',
              'questionCounter' => '32',
              'answer_type' => 'url',
              'answer_choices' => ''));
        $this->assertEqual(32, Profiles_ProfileQuestionHelper::maxCounter($questions));
    }

}


require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
