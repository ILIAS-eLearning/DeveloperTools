<?php

namespace ILIAS\Tools\Maintainers;

/**
 * Class Directory
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @package ILIAS\Tools\Maintainers
 */
class Directory extends JsonSerializable {

	const CLASSIC = 'Classic';
	const SERVICE = 'Service';
	/**
	 * @var string
	 */
	protected $maintenance_model = self::CLASSIC;
	/**
	 * @var \ILIAS\Tools\Maintainers\Maintainer
	 */
	protected $first_maintainer = '';
	/**
	 * @var \ILIAS\Tools\Maintainers\Maintainer
	 */
	protected $second_maintainer = '';
	/**
	 * @var \ILIAS\Tools\Maintainers\Maintainer[]
	 */
	protected $implicit_maintainers = array();
	/**
	 * @var \ILIAS\Tools\Maintainers\Maintainer
	 */
	protected $coordinator = '';
	/**
	 * @var \ILIAS\Tools\Maintainers\Maintainer
	 */
	protected $tester = '';
	/**
	 * @var \ILIAS\Tools\Maintainers\Maintainer
	 */
	protected $testcase_writer = '';
	/**
	 * @var string
	 */
	protected $path = '';
	/**
	 * @var \ILIAS\Tools\Maintainers\Component
	 */
	protected $belong_to_component;
	/**
	 * @var \ILIAS\Tools\Maintainers\Component[]
	 */
	protected $used_in_components = array();


	/**
	 * Directory constructor.
	 *
	 * @param string $path
	 */
	public function __construct($path) { $this->path = $path; }


	/**
	 * @return bool
	 */
	public function isMaintained() {
		switch ($this->getMaintenanceModel()) {
			case self::CLASSIC:
				$direct_maintainers = ($this->first_maintainer instanceof Maintainer
				                       && $this->first_maintainer->getUsername())
				                      || ($this->second_maintainer instanceof Maintainer
				                          && $this->second_maintainer->getUsername())
				                      || count($this->implicit_maintainers) > 0;

				$related_maintainers = $this->belong_to_component instanceof Component
				                       && ($this->belong_to_component->getFirstMaintainer()
				                                                     ->getUsername() != ''
				                           || $this->belong_to_component->getSecondMaintainer()
				                                                        ->getUsername() != '');

				return ($direct_maintainers || $related_maintainers);
			case self::SERVICE:
				return ($this->coordinator instanceof Maintainer);
			default:
				return false;
		}
	}


	public function inheritMaintainersFromComponent() {
		$this->populate();

		$force = false;

		if (($this->getFirstMaintainer()->getUsername() == ''
		    && $this->getBelongToComponent()->getFirstMaintainer()->getUsername() != '') || $force

		) {
			$this->setFirstMaintainer($this->getBelongToComponent()->getFirstMaintainer());
		}
		if (($this->getSecondMaintainer()->getUsername() == ''
		    && $this->getBelongToComponent()->getSecondMaintainer()->getUsername() != '') || $force

		) {
			$this->setSecondMaintainer($this->getBelongToComponent()->getSecondMaintainer());
		}
		if (($this->getTester()->getUsername() == ''
		    && $this->getBelongToComponent()->getTester()->getUsername() != '') || $force

		) {
			$this->setTester($this->getBelongToComponent()->getTester());
		}
		if (($this->getTestcaseWriter()->getUsername() == ''
		    && $this->getBelongToComponent()->getTestcaseWriter()->getUsername() != '') || $force

		) {
			$this->setTestcaseWriter($this->getBelongToComponent()->getTestcaseWriter());
		}
	}


	/**
	 * @param $from
	 * @param $to
	 * @return bool
	 */
	public function renameComponent($from, $to) {
		$this->populate();

		foreach ($this->getUsedinComponents() as $k => $component) {
			if ($component->getName() == $from) {
				$this->used_in_components[$k] = Component::getInstance($to);
			}
		}
		if ($this->getBelongToComponent()->getName() == $from) {
			$this->setBelongToComponent(Component::getInstance($to));
		}

		return true;
	}


	/**
	 * @return string
	 */
	public function getMaintenanceModel() {
		return $this->maintenance_model;
	}


	/**
	 * @param string $maintenance_model
	 */
	public function setMaintenanceModel($maintenance_model) {
		$this->maintenance_model = $maintenance_model;
	}


	/**
	 * @return Maintainer
	 */
	public function getFirstMaintainer() {
		return $this->first_maintainer;
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
	 * @param string $testcase_writer
	 */
	public function setTestcaseWriter($testcase_writer) {
		$this->testcase_writer = $testcase_writer;
	}


	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}


	/**
	 * @param string $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}


	/**
	 * @return \ILIAS\Tools\Maintainers\Component[]
	 */
	public function getUsedinComponents() {
		return $this->used_in_components;
	}


	/**
	 * @return bool
	 */
	public function hasComponents() {
		return ($this->getUsedinComponents() != array( 'None' ));
	}


	/**
	 * @param \ILIAS\Tools\Maintainers\Component[] $used_in_components
	 */
	public function setUsedinComponents(array $used_in_components) {
		$this->used_in_components = $used_in_components;
	}


	/**
	 * @return Maintainer
	 */
	public function getCoordinator() {
		return $this->coordinator;
	}


	/**
	 * @param Maintainer $coordinator
	 */
	public function setCoordinator(Maintainer $coordinator) {
		$this->coordinator = $coordinator;
	}


	/**
	 * @return Maintainer[]
	 */
	public function getImplicitMaintainers() {
		return $this->implicit_maintainers;
	}


	/**
	 * @param array $implicit_maintainers
	 */
	public function setImplicitMaintainers(array $implicit_maintainers) {
		$this->implicit_maintainers = $implicit_maintainers;
	}


	/**
	 * @return \ILIAS\Tools\Maintainers\Component
	 */
	public function getBelongToComponent() {
		return $this->belong_to_component;
	}


	/**
	 * @param \ILIAS\Tools\Maintainers\Component $belong_to_component
	 */
	public function setBelongToComponent(Component $belong_to_component) {
		$this->belong_to_component = $belong_to_component;
	}


	public function doPopulate() {
		$this->populateMaintainers();
		$this->populateComponents();
	}


	public function doStringyfy() {
		$this->stringifyMaintainers();
		$this->stringifyComponents();
	}


	private function populateComponents() {
		foreach ($this->used_in_components as $k => $component) {
			$c = Component::getInstance($component);
			$c->addDirectory($this);
			$this->used_in_components[$k] = $c;
		}
		$c = Component::getInstance($this->belong_to_component);
		$c->addDirectory($this);
		$this->belong_to_component = $c;
	}


	private function populateMaintainers() {
		$this->first_maintainer = Maintainer::fromString($this->first_maintainer);
		$this->second_maintainer = Maintainer::fromString($this->second_maintainer);
		$this->coordinator = Maintainer::fromString($this->coordinator);
		foreach ($this->implicit_maintainers as $k => $implicit_maintainer) {
			$this->implicit_maintainers[$k] = Maintainer::fromString($implicit_maintainer);
		}
		$this->tester = Maintainer::fromString($this->tester);
		$this->testcase_writer = Maintainer::fromString($this->testcase_writer);
	}


	private function stringifyMaintainers() {
		$this->first_maintainer = Maintainer::stringify($this->first_maintainer);
		$this->second_maintainer = Maintainer::stringify($this->second_maintainer);
		$this->coordinator = Maintainer::stringify($this->coordinator);
		foreach ($this->implicit_maintainers as $k => $implicit_maintainer) {
			$this->implicit_maintainers[$k] = Maintainer::stringify($implicit_maintainer);
		}
		$this->tester = Maintainer::stringify($this->tester);
		$this->testcase_writer = Maintainer::stringify($this->testcase_writer);
	}


	private function stringifyComponents() {
		foreach ($this->used_in_components as $k => $component) {
			$this->used_in_components[$k] = $component->getName();
		}
		$this->belong_to_component = $this->belong_to_component->getName();
	}
}