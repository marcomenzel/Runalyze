<?php
/**
 * This file contains class::Series
 * @package Runalyze\View\Plot
 */

namespace Runalyze\View\Plot;

use \Plot;

/**
 * Plot series
 * 
 * @author Hannes Christiansen
 * @package Runalyze\View\Plot
 */
class Series {
	/**
	 * @var string
	 */
	protected $Label;

	/**
	 * @var string
	 */
	protected $Color;

	/**
	 * @var array
	 */
	protected $Data;

	/**
	 * @var string
	 */
	protected $UnitString = '';

	/**
	 * @var int
	 */
	protected $UnitDecimals = 0;

	/**
	 * @var int
	 */
	protected $TickSize = false;

	/**
	 * @var int
	 */
	protected $TickDecimals = 0;

	/**
	 * @var boolean
	 */
	protected $ShowAverage = false;

	/**
	 * @var boolean
	 */
	protected $ShowMaximum = false;

	/**
	 * @var boolean
	 */
	protected $ShowMinimum = false;

	/**
	 * Set label
	 * @param string $label
	 */
	public function setLabel($label) {
		$this->Label = $label;
	}

	/**
	 * Set color
	 * @param string $color
	 */
	public function setColor($color) {
		$this->Color = $color;
	}

	/**
	 * Set data
	 * @param array $data
	 */
	public function setData(array $data) {
		$this->Data = $data;
	}

	/**
	 * Set properties
	 * @param \Plot $Plot
	 * @param int $yAxis
	 * @param boolean $addAnnotations [optional]
	 */
	public function addTo(Plot &$Plot, $yAxis, $addAnnotations = true) {
		if (empty($this->Data)) {
			return;
		}

		$Plot->Data[] = array('label' => $this->Label, 'color' => $this->Color, 'data' => $this->Data, 'yaxis' => $yAxis);

		if ($this->UnitString != '') {
			$Plot->addYUnit($yAxis, $this->UnitString, $this->UnitDecimals);
		}

		if ($this->TickSize !== false) {
			$Plot->setYTicks($yAxis, $this->TickSize, $this->TickDecimals);
		}

		if ($addAnnotations && $this->ShowAverage && !empty($this->Data)) {
			$avg = $this->avg();

			$Plot->addThreshold('y'.$yAxis, $avg, 'rgba(0,0,0,0.5)');
			$Plot->addAnnotation(0, $avg, '&oslash; '.$avg.' '.$this->UnitString);
		}

		if ($addAnnotations && $this->ShowMaximum) {
			$max = max($this->Data);
			$maxX = array_keys($this->Data, $max);

			$Plot->addAnnotation($maxX[0], round($max), round($max).$this->UnitString);
		}

		if ($addAnnotations && $this->ShowMinimum) {
			$min = min($this->Data);
			$minX = array_keys($this->Data, $min);

			$Plot->addAnnotation($minX[0], round($min), round($min).$this->UnitString);
		}
	}

	/**
	 * Average
	 * @return int
	 */
	protected function avg() {
		return round( array_sum($this->Data)/count($this->Data) );
	}
}