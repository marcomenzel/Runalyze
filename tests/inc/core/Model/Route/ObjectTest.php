<?php

namespace Runalyze\Model\Route;

/**
 * Generated by hand
 */
class ObjectTest extends \PHPUnit_Framework_TestCase {

	protected function simpleObject() {
		return new Object(array(
			Object::NAME => 'Test route',
			Object::CITIES => 'City A - City B',
			Object::DISTANCE => 3.14,
			Object::ELEVATION => 20,
			Object::ELEVATION_UP => 20,
			Object::ELEVATION_DOWN => 15,
			Object::GEOHASHES => array('u1xjhpfe7yvs', 'u1xjhzdtjx62'),
			Object::ELEVATIONS_ORIGINAL => array(195, 210),
			Object::ELEVATIONS_CORRECTED => array(200, 220),
			Object::ELEVATIONS_SOURCE => 'unknown',
			Object::IN_ROUTENET => 1
		));
	}

	public function testEmptyObject() {
		$T = new Object();

		$this->assertFalse($T->hasPositionData());
		$this->assertFalse($T->has(Object::NAME));
		$this->assertFalse($T->has(Object::DISTANCE));
		$this->assertFalse($T->inRoutenet());
	}

	public function testSimpleObject() {
		$T = $this->simpleObject();

		$this->assertEquals('Test route', $T->name());
		$this->assertEquals(array('City A', 'City B'), $T->citiesAsArray());
		$this->assertEquals(3.14, $T->distance());
		$this->assertEquals(20, $T->elevation());
		$this->assertEquals(20, $T->elevationUp());
		$this->assertEquals(15, $T->elevationDown());
		$this->assertEquals(array('u1xjhpfe7yvs', 'u1xjhzdtjx62'), $T->geohashes());
		$this->assertEquals(array(195, 210), $T->elevationsOriginal());
		$this->assertEquals(array(200, 220), $T->elevationsCorrected());
		$this->assertEquals('unknown', $T->get(Object::ELEVATIONS_SOURCE));
		$this->assertTrue($T->inRoutenet());
	}

	public function testSynchronization() {
		$T = $this->simpleObject();
		$T->synchronize();
		$T->forceToSetMinMaxFromGeohashes();
		$this->assertEquals('u1xjhpfe7y', $T->get(Object::STARTPOINT));
		$this->assertEquals('u1xjhzdtjx', $T->get(Object::ENDPOINT));

		$this->assertEquals('u1xjhpdt5z', $T->get(Object::MIN));
		$this->assertEquals('u1xjhzfemw', $T->get(Object::MAX));
	}


	/**
	 * @see https://github.com/Runalyze/Runalyze/issues/1172
	 */
	public function testPossibilityOfTooLargeCorrectedElevations() {
		$Object = new Object(array(
			Object::GEOHASHES => array('u1xjhxf507s1', 'u1xjhxf6b7s9', 'u1xjhxfd8jyw', 'u1xjhxfdx0cw', 'u1xjhxffrhw4', 'u1xjhxg4r0du', 'u1xjhxg6p6bq', 'u1xjhxgdn0fk', 'u1xjhxgcvgh0', 'u1xjhxu1tytn', 'u1xjhxu3s0j8'),
			Object::ELEVATIONS_ORIGINAL => array(240, 238, 240, 238, 238, 237, 236, 237, 240, 248, 259),
			Object::ELEVATIONS_CORRECTED => array(240, 240, 240, 240, 240, 237, 237, 237, 237, 237, 259, 259, 259, 259, 259)
		));

		$this->assertEquals(11, $Object->num());
		$this->assertEquals(11, count($Object->elevationsCorrected()));
	}

	public function testSetGeohashes() {
		// - set geohashes
		// - check min/max
	}

	/**
	 * @todo
	 */
	public function testSetLatitudesLongitudes() {
		// - set latitudes/longitudes
		// - check min/max
		// - check some geohashes
	}

	/**
	 * @todo
	 */
	public function testThatSetLatitudesLongitudesMustHaveExpectedSize() {
		// - create object with elevations array
		// - set latitudes/longitudes with larger array
		// - exception should be thrown
	}

}
