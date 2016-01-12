<?php

namespace Runalyze\Model\Activity;

/**
 * Generated by hand
 */
class EntityTest extends \PHPUnit_Framework_TestCase {

	public function testEmptyObject() {
		$A = new Entity();

		$this->assertTrue($A->weather()->isEmpty());
		$this->assertTrue($A->weather()->temperature()->isUnknown());
		$this->assertTrue($A->weather()->condition()->isUnknown());
		$this->assertTrue($A->weather()->humidity()->isUnknown());
		$this->assertTrue($A->weather()->pressure()->isUnknown());
		$this->assertTrue($A->weather()->windDegree()->isUnknown());
		$this->assertTrue($A->weather()->windSpeed()->isUnknown());

		$this->assertTrue($A->splits()->isEmpty());
		$this->assertTrue($A->partner()->isEmpty());
	}

	public function testEmptyWeatherValues() {
		$A = new Entity(array(
			Entity::TEMPERATURE => null,
			Entity::WINDDEG => null,
			Entity::WINDSPEED => null,
			Entity::PRESSURE => null,
			Entity::HUMIDITY => null
		));
		$B = new Entity(array(
			Entity::TEMPERATURE => '',
			Entity::WINDDEG => '',
			Entity::WINDSPEED => '',
			Entity::PRESSURE => '',
			Entity::HUMIDITY => ''
		));

		$this->assertTrue($A->weather()->temperature()->isUnknown());
		$this->assertTrue($B->weather()->temperature()->isUnknown());
		$this->assertTrue($A->weather()->windDegree()->isUnknown());
		$this->assertTrue($B->weather()->windDegree()->isUnknown());
		$this->assertTrue($A->weather()->windSpeed()->isUnknown());
		$this->assertTrue($B->weather()->windSpeed()->isUnknown());
		$this->assertTrue($A->weather()->pressure()->isUnknown());
		$this->assertTrue($B->weather()->pressure()->isUnknown());
		$this->assertTrue($A->weather()->humidity()->isUnknown());
		$this->assertTrue($B->weather()->humidity()->isUnknown());
	}

}
