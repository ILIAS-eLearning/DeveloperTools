<?php

namespace ILIAS\Tools\Maintainers;

use League\Flysystem\Filesystem;

/**
 * Class MarkdownWriter
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class MarkdownWriter {

	/**
	 * @var \ILIAS\Tools\Maintainers\Collector
	 **/
	protected $collector;


	/**
	 * MarkdownWriter constructor.
	 *
	 * @param \ILIAS\Tools\Maintainers\Collector $collector
	 */
	public function __construct(Collector $collector) { $this->collector = &$collector; }


	/**
	 * @return \ILIAS\Tools\Maintainers\Collector
	 */
	public function getCollector() {
		return $this->collector;
	}


	/**
	 * @param \ILIAS\Tools\Maintainers\Collector $collector
	 */
	public function setCollector(Collector $collector) {
		$this->collector = $collector;
	}


	/**
	 * @param \League\Flysystem\Filesystem $filesystem
	 * @param string                       $path_to_file
	 */
	public function writeMD(Filesystem $filesystem, $path_to_file = 'docs/documentation/maintenance.md') {
		if (!$filesystem->has($path_to_file)) {
			$filesystem->write($path_to_file, '');
		}

		/*
		 * * **Administration**
	* 1st Maintainer: [Alexander Killing]
	* 2nd Maintainer: [Stefan Meyer]
	* Testcases: [Matthias Kunkel]
	* Tester: [Matthias Kunkel]
		 */

		// $md = "The code base is deviced in several components which are maintained in the Classic-Maintenance-Model:\n";

		$seperator = "<!-- REMOVE -->";
		$md = strstr($filesystem->read($path_to_file), $seperator, true) . $seperator . "\n";

		//		$components = $this->getCollector()->getComponents();
		$components = Component::getRegistredInstances();
		ksort($components);

		foreach ($components as $component) {
			$component->populate();
			$name = $component->getName();
			if ($name == 'All' || $name == 'None') {
				continue;
			}
			if ($component->getModel() == Directory::SERVICE) {
				continue;
			}

			if (!$component->getFirstMaintainer()->getUsername()
				&& !$component->getSecondMaintainer()->getUsername()
			) {
				continue;
			}
			$md .= "* **{$name}**\n";
			$md .= "\t* 1st Maintainer: {$component->getFirstMaintainerOrMissing()}\n";
			$md .= "\t* 2nd Maintainer: {$component->getSecondMaintainerOrMissing()}\n";
			$md .= "\t* Testcases: {$component->getTestcaseWriterOrMissing()}\n";
			$md .= "\t* Tester: {$component->getTesterOrMissing()}\n";
			//			$md .= "\t* Used in Directories: ";
			//			foreach ($component->getDirectories() as $directory) {
			//				$md .= "{$directory->getPath()}, ";
			//			}

			$md .= "\n";
		}

		$md .= "\nComponents in the Coordinator Model [Coordinator Model](maintenance-coordinator.md):\n\n";

		foreach ($components as $component) {
			$component->populate();
			$name = $component->getName();
			if ($name == 'All' || $name == 'None') {
				// continue;
			}
			if ($component->getModel() == Directory::CLASSIC) {
				continue;
			}
			$md .= "* **{$name}**\n";
			$md .= "\t* Coordinators: ";
			$coordinator_strings = [];
			foreach ($component->getCoordinators() as $coordinator) {
				$coordinator_strings[] = $coordinator->getLinkedProfile();
			}
			$md .= implode(", ", $coordinator_strings);
			$md .= "\n";
			$md .= "\t* Used in Directories: ";
			$used_strings = [];
			foreach ($component->getDirectories() as $directory) {
				$used_strings[] = $directory->getPath();
			}
			$md .= implode(", ", $used_strings);

			$md .= "\n";
		}

		$md .= "\n\nThe following directories are currently maintained under the [Coordinator Model](maintenance-coordinator.md):\n\n";
		/**
		 * @var $coordinator \ILIAS\Tools\Maintainers\Maintainer
		 */
		$directories1 = $this->getCollector()->getByModell(Directory::SERVICE);
		ksort($directories1);
		foreach ($directories1 as $directory) {
			$directory->populate();

			$md .= "* {$directory->getPath()}\n";
		}

		$md .= "\n\nThe following directories are currently unmaintained:\n\n";

		$directories2 = $this->getCollector()->getUnmaintained();
		sort($directories2);
		foreach ($directories2 as $directory) {
			$directory->populate();
			$md .= "* {$directory->getPath()}\n";
		}
		$filesystem->update($path_to_file, $md);
	}
}
