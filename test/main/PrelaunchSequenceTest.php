<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_App.php');

class PrelaunchSequenceTest extends UnitTestCase {
    protected $originalLaunchState;
    protected $originalCompletedSteps;

    protected $prelaunchSteps;

    public function __construct() {
        $this->prelaunchSteps = array(
                array('name' => 'About Your Site', 'state' => 'incomplete',
                'controller' => 'admin', 'action' => 'appProfile', 'displayName' =>  xg_text('ABOUT_YOUR_SITE')),
                array('name' => 'Features', 'state' => 'incomplete',
                'controller' => 'feature', 'action' => 'add', 'displayName' => xg_text('FEATURES')),
                array('name' => 'Appearance', 'state' => 'incomplete',
                'controller' => 'appearance', 'action' => 'edit', 'displayName' => xg_text('APPEARANCE')),
        );
    }

    public function setUp() {
        $mainWidget = W_Cache::getWidget('main');
        $this->originalLaunchState = $mainWidget->config['launched'];
        $this->originalCompletedSteps = $mainWidget->config['prelaunchStepsCompleted'];
        $mainWidget->config['launched'] = 0;
        XG_App::_setCompletedSteps(NULL);
    }

    public function tearDown() {
        $mainWidget = W_Cache::getWidget('main');
        $mainWidget->config['launched'] = $this->originalLaunchState;
        $mainWidget->config['prelaunchStepsCompleted'] = $this->originalCompletedSteps;
        $mainWidget->saveConfig();
    }

    public function testMarkStepCompleted() {
        $this->assertIdentical(XG_App::_getCompletedSteps(), array());
        $steps = array();

        //  Verify that marking a step completed adds it to the list
        for ($n = 1; $n <= 10; $n++) {
            $steps[] = "test$n";
            XG_App::markStepCompleted("test$n");
            $this->assertIdentical(XG_App::_getCompletedSteps(), $steps);
        }

        //  Verify that marking an already completed step completed does nothing
        for ($n = 1; $n <= 10; $n+=2) {
            XG_App::markStepCompleted("test$n");
            $this->assertIdentical(XG_App::_getCompletedSteps(), $steps);
        }
    }

    public function testAllStepsCompleted() {
        foreach ($this->prelaunchSteps as $idx => $step) {
            XG_App::markStepCompleted($step['name']);
            if ($idx < count($this->prelaunchSteps) - 1) {
                $this->assertFalse(XG_App::allStepsCompleted());
            }
            else {
                $this->assertTrue(XG_App::allStepsCompleted());
            }
        }
    }

    public function testGetLaunchbarSteps() {
        $expectedSteps = $this->prelaunchSteps;

        $this->assertIdentical(XG_App::getLaunchbarSteps(), $expectedSteps);
        foreach ($expectedSteps as $idx => $step) {
            XG_App::markStepCompleted($step['name']);
            $expectedSteps[$idx]['state'] = 'complete';
            $this->assertIdentical(XG_App::getLaunchbarSteps(true), $expectedSteps);
        }
    }

    public function testCurrentLaunchStepRoute() {
        $steps = $this->prelaunchSteps;
        //  Test next step when completing steps in order
        $previousStep = NULL;
        foreach ($steps as $step) {
            if ($previousStep) {
                XG_App::markStepCompleted($previousStep);
            }
            $this->assertIdentical(XG_App::currentLaunchStepRoute(),
                    array('widgetName' => 'main',
                    'controllerName' => $step['controller'],
                    'actionName' => $step['action']));
            $previousStep = $step['name'];
        }
    }

    public function testCurrentLaunchStepRoute2() {
        $steps = array_reverse($this->prelaunchSteps);
        //  Test next step when completing steps in reverse order
        $previousStep = NULL;
        foreach ($steps as $step) {
            if ($previousStep) {
                XG_App::markStepCompleted($previousStep);
            }
            $this->assertIdentical(XG_App::currentLaunchStepRoute(),
                    array('widgetName' => 'main',
                    'controllerName' => $this->prelaunchSteps[0]['controller'],
                    'actionName' => $this->prelaunchSteps[0]['action']));
            $previousStep = $step['name'];
        }
    }

}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';



