<?php	# $Id: $
require_once $_SERVER['DOCUMENT_ROOT'] . '/test/test_header.php';
XG_App::includeFileOnce('/lib/XG_DateHelper.php');

class XG_DateHelperTest extends UnitTestCase {
    public function setUp() {
        XG_TestHelper::setCurrentWidget('events');
        XG_TestHelper::deleteTestObjects();
    }

    //
    public function testDates() { # void
		$o = new XG_DateHelper;
		$this->assertEqual($o->monthRange('2006-12','2007-02'),array('2006-12','2007-01','2007-02'));
		$this->assertEqual($o->monthRange('2007-1','2007-3'),array('2007-01','2007-02','2007-03'));
		$this->assertEqual($o->monthRange('2007-3','2007-1'),array());
		$this->assertEqual($o->monthRange('safasdf','sfasdf'),array());

		$this->assertEqual($o->dayRange('sdsa','34t3g45'),array());
		$this->assertEqual($o->dayRange('2006-12','2007-02'),array());
		$this->assertEqual($o->dayRange('2006-12-30','2007-01-02'),array('2006-12-30','2006-12-31','2007-01-01','2007-01-02'));
    }

    //
    public function testCalendar () { # void
		$cal = XG_DateHelper::calendar('2008','02');
		$this->assertEqual($cal,array(
			qw('5>1 6>2'),
			qw('7>3 1>4 2>5 3>6 4>7 5>8 6>9'),
			qw('7>10 1>11 2>12 3>13 4>14 5>15 6>16'),
			qw('7>17 1>18 2>19 3>20 4>21 5>22 6>23'),
			qw('7>24 1>25 2>26 3>27 4>28 5>29'),
			));
        return;
    }


    public function tearDown() {
        XG_TestHelper::deleteTestObjects();
    }
}

require $_SERVER['DOCUMENT_ROOT'] . '/test/test_footer.php';
?>
