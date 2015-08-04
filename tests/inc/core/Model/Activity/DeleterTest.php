<?php

namespace Runalyze\Model\Activity;

use Runalyze\Configuration;
use Runalyze\Model;
use Runalyze\Data\Weather;

use PDO;

/**
 * Generated by hand
 */
class DeleterTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \PDO
	 */
	protected $PDO;

	protected function setUp() {
		$this->PDO = \DB::getInstance();
		$this->PDO->exec('INSERT INTO `'.PREFIX.'sport` (`name`,`id`,`kcal`,`outside`,`accountid`) VALUES("",1,600,1,0)');
		$this->PDO->exec('INSERT INTO `'.PREFIX.'sport` (`name`,`id`,`kcal`,`outside`,`accountid`) VALUES("",2,400,0,0)');
        }

	protected function tearDown() {
		$this->PDO->exec('TRUNCATE TABLE `'.PREFIX.'training`');
		$this->PDO->exec('TRUNCATE TABLE `'.PREFIX.'trackdata`');
		$this->PDO->exec('TRUNCATE TABLE `'.PREFIX.'swimdata`');
		$this->PDO->exec('TRUNCATE TABLE `'.PREFIX.'route`');
		$this->PDO->exec('TRUNCATE TABLE `'.PREFIX.'sport`');
	}

	/**
	 * @param array $data
	 * @return int
	 */
	protected function insert(array $data) {
		$Inserter = new Inserter($this->PDO, new Object($data));
		$Inserter->setAccountID(0);
		$Inserter->insert();

		return $Inserter->insertedID();
	}

	/**
	 * @param int $id
	 */
	protected function delete($id) {
		$Deleter = new Deleter($this->PDO, $this->fetch($id));
		$Deleter->setAccountID(0);
		$Deleter->delete();
	}

	/**
	 * @param int $id
	 * @return \Runalyze\Model\Activity\Object
	 */
	protected function fetch($id) {
		return new Object(
			$this->PDO->query('SELECT * FROM `'.PREFIX.'training` WHERE `id`="'.$id.'" AND `accountid`=0')->fetch(PDO::FETCH_ASSOC)
		);
	}

	/**
	 * @expectedException \PHPUnit_Framework_Error
	 */
	public function testWrongObject() {
		new Deleter($this->PDO, new Model\Trackdata\Object);
	}

	public function testStartTimeUpdate() {
		$current = time();
		$old = mktime(0,0,0,1,1,2006);
		$older = mktime(0,0,0,1,1,2003);
		$oldest = mktime(0,0,0,1,1,2000);

		Configuration::Data()->updateStartTime($current);

		$this->insert(array(Object::TIMESTAMP => $current));
		$oldId = $this->insert(array(Object::TIMESTAMP => $old));
		$olderId = $this->insert(array(Object::TIMESTAMP => $older));
		$oldestId = $this->insert(array(Object::TIMESTAMP => $oldest));

		$this->assertEquals($oldest, Configuration::Data()->startTime());

		$this->delete($olderId);
		$this->assertEquals($oldest, Configuration::Data()->startTime());

		$this->delete($oldestId);
		$this->assertEquals($old, Configuration::Data()->startTime());

		$this->delete($oldId);
		$this->assertEquals($current, Configuration::Data()->startTime());
	}

	public function testVDOTstatisticsForChanges() {
		$newId = $this->insert(array(
			Object::TIMESTAMP => time(),
			Object::DISTANCE => 10,
			Object::TIME_IN_SECONDS => 30*60,
			Object::HR_AVG => 150,
			Object::SPORTID => Configuration::General()->runningSport(),
			Object::TYPEID => Configuration::General()->competitionType(),
			Object::USE_VDOT => true
		));

		$this->assertNotEquals(0, Configuration::Data()->vdotShape());
		$this->assertNotEquals(1, Configuration::Data()->vdotFactor());

		$this->delete($newId);

		$this->assertEquals(0, Configuration::Data()->vdotShape());
		$this->assertEquals(1, Configuration::Data()->vdotFactor());
	}

	public function testVDOTstatisticsForNoChanges() {
		$IDs = array();
		$IDs[] = $this->insert(array(
			Object::TIMESTAMP => time(),
			Object::DISTANCE => 10,
			Object::TIME_IN_SECONDS => 30*60,
			Object::HR_AVG => 150,
			Object::SPORTID => Configuration::General()->runningSport() + 1,
			Object::TYPEID => Configuration::General()->competitionType(),
			Object::USE_VDOT => true
		));
		$IDs[] = $this->insert(array(
			Object::TIMESTAMP => time(),
			Object::DISTANCE => 10,
			Object::TIME_IN_SECONDS => 30*60,
			Object::HR_AVG => 150,
			Object::SPORTID => Configuration::General()->runningSport(),
			Object::TYPEID => Configuration::General()->competitionType(),
			Object::USE_VDOT => false
		));
		$IDs[] = $this->insert(array(
			Object::TIMESTAMP => time(),
			Object::DISTANCE => 10,
			Object::TIME_IN_SECONDS => 30*60,
			Object::SPORTID => Configuration::General()->runningSport(),
			Object::TYPEID => Configuration::General()->competitionType(),
			Object::USE_VDOT => true
		));
		$IDs[] = $this->insert(array(
			Object::TIMESTAMP => time() - 365*DAY_IN_S,
			Object::DISTANCE => 10,
			Object::TIME_IN_SECONDS => 30*60,
			Object::HR_AVG => 150,
			Object::SPORTID => Configuration::General()->runningSport(),
			Object::TYPEID => Configuration::General()->competitionType(),
			Object::USE_VDOT => true
		));

		Configuration::Data()->updateVdotShape(62.15);
		Configuration::Data()->updateVdotCorrector(0.789);

		foreach ($IDs as $id) {
			$this->delete($id);
		}

		$this->assertEquals(62.15, Configuration::Data()->vdotShape());
		$this->assertEquals(0.789, Configuration::Data()->vdotCorrector());
	}

	public function testUpdatingBasicEndurance() {
		$ignoredId1 = $this->insert(array(
			Object::TIMESTAMP => time() - 365*DAY_IN_S,
			Object::DISTANCE => 30,
			Object::TIME_IN_SECONDS => 30*60*3,
			Object::SPORTID => Configuration::General()->runningSport()
		));
		$ignoredId2 = $this->insert(array(
			Object::TIMESTAMP => time(),
			Object::DISTANCE => 30,
			Object::TIME_IN_SECONDS => 30*60*3,
			Object::SPORTID => Configuration::General()->runningSport() + 1
		));
		$relevantId = $this->insert(array(
			Object::TIMESTAMP => time(),
			Object::DISTANCE => 30,
			Object::TIME_IN_SECONDS => 30*60*3,
			Object::SPORTID => Configuration::General()->runningSport()
		));

		$this->assertNotEquals(0, Configuration::Data()->basicEndurance());

		$this->delete($ignoredId1);
		$this->assertNotEquals(0, Configuration::Data()->basicEndurance());

		$this->delete($ignoredId2);
		$this->assertNotEquals(0, Configuration::Data()->basicEndurance());

		$this->delete($relevantId);
		$this->assertEquals(0, Configuration::Data()->basicEndurance());
	}

	public function testDeletionOfRoute() {
		$this->PDO->exec('INSERT INTO `'.PREFIX.'route` (`accountid`) VALUES(0)');
		$routeID = $this->PDO->lastInsertId();

		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` (`routeid`,`accountid`) VALUES('.$routeID.',0)');
		$activityID = $this->PDO->lastInsertId();

		$this->delete($activityID);

		$this->assertEquals(array(), $this->PDO->query('SELECT `id` FROM `'.PREFIX.'route` WHERE `id`='.$routeID)->fetchAll());
		$this->assertEquals(array(), $this->PDO->query('SELECT `id` FROM `'.PREFIX.'training` WHERE `id`='.$activityID)->fetchAll());
	}

	public function testDeletionOfTrackdata() {
		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` (`accountid`) VALUES(0)');
		$activityID = $this->PDO->lastInsertId();

		$this->PDO->exec('INSERT INTO `'.PREFIX.'trackdata` (`activityid`, `accountid`) VALUES('.$activityID.', 0)');

		$this->delete($activityID);

		$this->assertEquals(array(), $this->PDO->query('SELECT `activityid` FROM `'.PREFIX.'trackdata` WHERE `activityid`='.$activityID)->fetchAll());
		$this->assertEquals(array(), $this->PDO->query('SELECT `id` FROM `'.PREFIX.'training` WHERE `id`='.$activityID)->fetchAll());
	}

	public function testDeletionOfSwimdata() {
		$this->PDO->exec('INSERT INTO `'.PREFIX.'training` (`accountid`) VALUES(0)');
		$activityID = $this->PDO->lastInsertId();

		$this->PDO->exec('INSERT INTO `'.PREFIX.'swimdata` (`activityid`, `accountid`) VALUES('.$activityID.', 0)');

		$this->delete($activityID);

		$this->assertEquals(array(), $this->PDO->query('SELECT `activityid` FROM `'.PREFIX.'swimdata` WHERE `activityid`='.$activityID)->fetchAll());
		$this->assertEquals(array(), $this->PDO->query('SELECT `id` FROM `'.PREFIX.'training` WHERE `id`='.$activityID)->fetchAll());
	}

}
