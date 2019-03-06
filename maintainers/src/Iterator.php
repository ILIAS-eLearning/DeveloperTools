<?php

namespace ILIAS\Tools\Maintainers;

use ILIAS\Tools\Maintainers\MarkdownWriter;
use League\CLImate\CLImate;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Plugin\ListPaths;

/**
 * Class Iterator
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @package ILIAS\Tools\Maintainers
 */
class Iterator {

	const TEMPLATE = 'Customizing/global/tools/maintainers/template.json';
	const FILENAME = 'maintenance.json';
	/**
	 * @var Filesystem
	 */
	protected $filesystem;
	/**
	 * @var string
	 */
	protected $base_path = '';
	/**
	 * @var \ILIAS\Tools\Maintainers\Collector
	 */
	protected $collector;


	/**
	 * Iterator constructor.
	 *
	 * @param $base_path
	 * @throws \Exception
	 */
	public function __construct($base_path) {
		$this->base_path = $base_path;
		$adapter = new Local($this->base_path);
		$this->filesystem = new Filesystem($adapter);
		$this->filesystem->addPlugin(new ListPaths());
		$this->collector = new Collector();
	}


	/**
	 * @param $directory
	 * @param \League\CLImate\CLImate|null $cli
	 */
	protected function run($directory, CLImate $cli = null, $from = null, $to = null) {
		foreach ($this->getFilesystem()->listPaths($directory, false) as $path) {
			if ($this->getFilesystem()->get($path)->isFile()) {
				continue;
			}
			if ($cli) {
				$cli->out('Investigating ' . $path);
			}
			$json = $path . '/' . self::FILENAME;
			$Directory = new Directory($path);
			if (!$this->getFilesystem()->has($json)) {
				if ($cli) {
					$cli->out('Create new maintenance.json in ' . $path);
				}
				$this->getFilesystem()->write($json, $Directory->serializeAsJson());
			} else {
				$Directory->unserializeFromJson($this->getFilesystem()->read($json));
				$Directory->populate();
				foreach ($Directory->getUsedinComponents() as $Component) {
					$this->collector->addComponent($Component);
				}
				$this->collector->addComponent($Directory->getBelongToComponent());
			}
			$Directory->populate();
			if ($from && $to) {
				if($cli)
				$cli->out('Renaming');
				$Directory->renameComponent($from, $to);
			}
			$Directory->inheritMaintainersFromComponent($cli);
			if ($Directory->isMaintained()) {
				$this->collector->addMaintained($Directory);
			} else {
				$this->collector->addUnmaintained($Directory);
			}

			$this->getFilesystem()->update($json, $Directory->serializeAsJson());
		}
	}


	public function loadFiles() {
		Maintainer::loadMaintainerJson($this->filesystem);
		Component::loadComponentsJson($this->filesystem);
	}


	/**
	 * @return array
	 */
	protected function getTemplateAsArray() {
		static $templateStdClass;
		if (!$templateStdClass) {
			$templateStdClass = $this->getJsonAsArray(self::TEMPLATE);
		}

		return $templateStdClass;
	}


	/**
	 * @param array $directories
	 * @param \League\CLImate\CLImate|null $cli
	 * @param string $from rename Component
	 * @param string $to   new name of component
	 */
	public function runFor(array $directories, CLImate $cli = null, $from = null, $to = null) {
		$this->loadFiles();
		foreach ($directories as $directory) {
			$this->run($directory, $cli, $from, $to);
		}
		$write = new MarkdownWriter($this->getCollector());
		$write->writeMD($this->getFilesystem());
		Maintainer::writeMaintainerJson($this->filesystem);
		Component::writeComponentsJson($this->filesystem);
	}


	/**
	 * @param $file
	 * @return array
	 */
	protected function getJsonAsArray($file) {
		return json_decode($this->filesystem->read($file), true);
	}


	/**
	 * @return string
	 */
	public function getBasePath() {
		return $this->base_path;
	}


	/**
	 * @param string $base_path
	 */
	public function setBasePath($base_path) {
		$this->base_path = $base_path;
	}


	/**
	 * @return \League\Flysystem\Filesystem
	 */
	public function getFilesystem() {
		return $this->filesystem;
	}


	/**
	 * @param \League\Flysystem\Filesystem $filesystem
	 */
	public function setFilesystem(Filesystem $filesystem) {
		$this->filesystem = $filesystem;
	}


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
}