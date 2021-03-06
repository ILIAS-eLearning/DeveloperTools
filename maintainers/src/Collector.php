<?php

namespace ILIAS\Tools\Maintainers;

/**
 * Class Collector
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @package ILIAS\Tools\Maintainers
 */
class Collector {

	/**
	 * @var \ILIAS\Tools\Maintainers\Component[]
	 */
	protected $components = array();
	/**
	 * @var \ILIAS\Tools\Maintainers\Directory[]
	 */
	protected $maintained = array();
	/**
	 * @var \ILIAS\Tools\Maintainers\Directory[]
	 */
	protected $unmaintained = array();
	/**
	 * @var array
	 */
	protected $by_modell = array();


	/**
	 * @param \ILIAS\Tools\Maintainers\Component $component
	 */
	public function addComponent(Component $component) {
		$this->components[$component->getName()] = $component;
	}


	/**
	 * @param \ILIAS\Tools\Maintainers\Directory $directory
	 */
	public function addUnmaintained(Directory $directory) {
		$this->unmaintained[$directory->getPath()] = $directory;
	}


	/**
	 * @param \ILIAS\Tools\Maintainers\Directory $directory
	 */
	public function addMaintained(Directory $directory) {
		$this->maintained[$directory->getPath()] = $directory;
		$this->by_modell[$directory->getMaintenanceModel()][$directory->getPath()] = $directory;
	}


	/**
	 * @return \ILIAS\Tools\Maintainers\Directory[]
	 */
	public function getMaintained() {
		return $this->maintained;
	}


	/**
	 * @return \ILIAS\Tools\Maintainers\Directory[]
	 */
	public function getUnmaintained() {
		return $this->unmaintained;
	}


	/**
	 * @return int
	 */
	public function howManyMaintained() {
		return count($this->maintained);
	}


	/**
	 * @return int
	 */
	public function howManyUnmaintained() {
		return count($this->unmaintained);
	}


	/**
	 * @return int
	 */
	public function howManyComponents() {
		return count($this->components);
	}


	/**
	 * @return \ILIAS\Tools\Maintainers\Component[]
	 */
	public function getComponents() {
		return $this->components;
	}


	/**
	 * @param \ILIAS\Tools\Maintainers\Component[] $components
	 */
	public function setComponents(array $components) {
		$this->components = $components;
	}


	/**
	 * @return string
	 */
	public function getAvailableComponentsAsString() {
		$av = array_keys($this->getComponents());
		sort($av);

		return implode("\n", $av);
	}


	/**
	 * @return \ILIAS\Tools\Maintainers\Directory[]
	 */
	public function getByModell($modell) {
		return $this->by_modell[$modell] ? $this->by_modell[$modell] : array();
	}
}