<?php

namespace ILIAS\Tools\Maintainers;

use League\Flysystem\Filesystem;

/**
 * Class Component
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @package ILIAS\Tools\Maintainers
 */
class Component extends JsonSerializable {

	const MISSING = "MISSING";

	/**
	 * @var Component[]
	 */
	protected static $registredInstances = array();


	/**
	 * Component constructor.
	 *
	 * @param string $name
	 */
	public function __construct($name) { $this->name = $name; }


	/**
	 * @param string $name
	 *
	 * @return \ILIAS\Tools\Maintainers\Component
	 */
	public static function getInstance($name) {
		if (!$name || !is_string($name)) {
			$name = "None";
		}
		if (!key_exists($name, self::$registredInstances)) {
			self::$registredInstances[$name] = new self($name);
		}
		self::$registredInstances[$name]->populate();

		return self::$registredInstances[$name];
	}


	/**
	 * @return \ILIAS\Tools\Maintainers\Component[]
	 */
	public static function getRegistredInstances() {
		return self::$registredInstances;
	}


	/**
	 * @var \ILIAS\Tools\Maintainers\Directory[]
	 */
	protected $directories = array();
	/**
	 * @var string
	 */
	protected $name = '';
	/**
	 * @var \ILIAS\Tools\Maintainers\Maintainer
	 */
	protected $first_maintainer = "";
	/**
	 * @var \ILIAS\Tools\Maintainers\Maintainer
	 */
	protected $second_maintainer = "";
	/**
	 * @var \ILIAS\Tools\Maintainers\Maintainer
	 */
	protected $tester = "";
	/**
	 * @var \ILIAS\Tools\Maintainers\Maintainer
	 */
	protected $testcase_writer = "";
	/**
	 * @var string
	 */
	protected $model = Directory::CLASSIC;
	/**
	 * @var \ILIAS\Tools\Maintainers\Maintainer[]
	 */
	protected $coordinators = array();


	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}


	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}


	/**
	 * @return \ILIAS\Tools\Maintainers\Directory[]
	 */
	public function getDirectories() {
		return $this->directories;
	}


	/**
	 * @param \ILIAS\Tools\Maintainers\Directory[] $directories
	 */
	public function setDirectories(array $directories) {
		$this->directories = $directories;
	}


	/**
	 * @param \ILIAS\Tools\Maintainers\Directory $directory
	 */
	public function addDirectory(Directory $directory) {
		$this->directories[$directory->getPath()] = $directory;
	}


	/**
	 * @param \League\Flysystem\Filesystem $filesystem
	 * @param string                       $path_to_file
	 */
	public static function writeComponentsJson(Filesystem $filesystem, $path_to_file = 'Customizing/global/tools/maintainers/components.json') {
		if (!$filesystem->has($path_to_file)) {
			$filesystem->write($path_to_file, '');
		}
		$components = array();
		foreach (self::getRegistredInstances() as $component) {
			if ($component->getName() == 'None'
			    || $component->getName() == 'All'
			) {
				continue;
			}
			$components[$component->getName()] = $component->serialize();
		}
		asort($components);

		$filesystem->update($path_to_file, JsonSerializable::json_encode($components));
	}


	/**
	 * @param \League\Flysystem\Filesystem $filesystem
	 * @param string                       $path_to_file
	 */
	public static function loadComponentsJson(Filesystem $filesystem, $path_to_file = 'Customizing/global/tools/maintainers/components.json') {
		if (!$filesystem->has($path_to_file)) {
			$filesystem->write($path_to_file, '[]');
		}

		foreach (json_decode($filesystem->read($path_to_file)) as $item) {
			$new = new self('');
			$new->unserialize($item);
			$new->setDirectories(array());

			self::$registredInstances[$new->getName()] = $new;
		}
	}


	/**
	 * @param $property_name
	 *
	 * @return string
	 */
	protected function getTextOrMissing($property_name) {
		$value = $this->{$property_name};
		if ($value instanceof Maintainer) {
			return $value->getLinkedProfile($property_name);
		}


		return $value ? $value : self::MISSING;
	}


	/**
	 * @return Maintainer
	 */
	public function getFirstMaintainer() {
		return $this->first_maintainer;
	}


	/**
	 * @return string
	 */
	public function getFirstMaintainerOrMissing() {
		return $this->getTextOrMissing('first_maintainer');
	}


	/**
	 * @param Maintainer $first_maintainer
	 */
	public function setFirstMaintainer(Maintainer $first_maintainer) {
		$this->first_maintainer = $first_maintainer;
	}


	/**
	 * @return Maintainer
	 */
	public function getSecondMaintainer() {
		return $this->second_maintainer;
	}


	/**
	 * @return string
	 */
	public function getSecondMaintainerOrMissing() {
		return $this->getTextOrMissing('second_maintainer');
	}


	/**
	 * @param Maintainer $second_maintainer
	 */
	public function setSecondMaintainer(Maintainer $second_maintainer) {
		$this->second_maintainer = $second_maintainer;
	}


	/**
	 * @return Maintainer
	 */
	public function getTester() {
		return $this->tester;
	}


	/**
	 * @return string
	 */
	public function getTesterOrMissing() {
		return $this->getTextOrMissing('tester');
	}


	/**
	 * @param Maintainer $tester
	 */
	public function setTester(Maintainer $tester) {
		$this->tester = $tester;
	}


	/**
	 * @return Maintainer
	 */
	public function getTestcaseWriter() {
		return $this->testcase_writer;
	}


	/**
	 * @return string
	 */
	public function getTestcaseWriterOrMissing() {
		return $this->getTextOrMissing('testcase_writer');
	}


	/**
	 * @param Maintainer $testcase_writer
	 */
	public function setTestcaseWriter(Maintainer $testcase_writer) {
		$this->testcase_writer = $testcase_writer;
	}


	public function doPopulate() {
		$this->populateMaintainers();
	}


	public function doStringyfy() {
		$this->stringifyMaintainers();
		$this->directories = array_keys($this->directories);
	}


	/**
	 * @return string
	 */
	public function getModel() {
		return $this->model;
	}


	/**
	 * @param string $model
	 */
	public function setModel($model) {
		$this->model = $model;
	}


	/**
	 * @return \ILIAS\Tools\Maintainers\Maintainer[]
	 */
	public function getCoordinators() {
		return $this->coordinators;
	}


	/**
	 * @param \ILIAS\Tools\Maintainers\Maintainer[] $coordinators
	 */
	public function setCoordinators(array $coordinators) {
		$this->coordinators = $coordinators;
	}


	protected function populateMaintainers() {
		$this->first_maintainer = Maintainer::fromString($this->first_maintainer);
		$this->second_maintainer = Maintainer::fromString($this->second_maintainer);
		$this->tester = Maintainer::fromString($this->tester);
		$this->testcase_writer = Maintainer::fromString($this->testcase_writer);

		foreach ($this->coordinators as $k => $coordinator) {
			$this->coordinators[$k] = Maintainer::fromString($coordinator);
		}
	}


	protected function stringifyMaintainers() {
		$this->first_maintainer = Maintainer::stringify($this->first_maintainer);
		$this->second_maintainer = Maintainer::stringify($this->second_maintainer);
		$this->tester = Maintainer::stringify($this->tester);
		$this->testcase_writer = Maintainer::stringify($this->testcase_writer);
		foreach ($this->coordinators as $k => $coordinator) {
			$this->coordinators[$k] = Maintainer::stringify($coordinator);
		}
	}
}