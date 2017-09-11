<?php

namespace ILIAS\Tools\Maintainers;

use League\Flysystem\Filesystem;

/**
 * Class Maintainer
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @package ILIAS\Tools\Maintainers
 */
class Maintainer extends JsonSerializable {

	const TESTER_MISSING = "[TESTERS MISSING](http://www.ilias.de/docu/goto_docu_pg_64423_4793.html)";
	/**
	 * @var \ILIAS\Tools\Maintainers\Maintainer[]
	 */
	protected static $registeredMaintainers = array();
	/**
	 * @var string
	 */
	protected $username = '';
	/**
	 * @var int
	 */
	protected $user_id = 0;


	/**
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}


	/**
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}


	/**
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
	}


	/**
	 * @param int $user_id
	 */
	public function setUserId($user_id) {
		$this->user_id = $user_id;
	}


	/**
	 * @return string
	 */
	public function getLinkedProfile($property_name = null) {
		switch (true) {
			case ($this->getUserId() == 0 && $this->getUsername() == ''):
				switch ($property_name) {
					case 'tester':
					case 'testcase_writer':
						return self::TESTER_MISSING;
						break;
					default;
						return "MISSING";
				}
			case ($this->getUserId() == 0 && !$this->getUsername() == ''):
				return $this->getUsername();
		}

		return "[{$this->getUsername()}](http://www.ilias.de/docu/goto_docu_usr_{$this->getUserId()}.html)";
	}


	/**
	 * @param $string
	 *
	 * @return \ILIAS\Tools\Maintainers\Maintainer
	 */
	public static function fromString($string) {
		if (!is_string($string) || !$string) {
			return new self();
		}

		if (key_exists($string, self::$registeredMaintainers)) {
			return self::$registeredMaintainers[$string];
		}

		if (preg_match('/(.*)\\(([\\d]*)\\)/uUm', $string, $matches)) {
			$username = (string)$matches[1];
			if (key_exists($string, self::$registeredMaintainers)) {
				$n = self::$registeredMaintainers[$username];
			} else {
				$n = new self();
			}
			$n->setUsername($username);
			$user_id = (key_exists($username, self::$registeredMaintainers) ? self::$registeredMaintainers[$username]->getUserId() : (int)$matches[2]);
			$n->setUserId($user_id);
		} else {
			$n = new self();
			$n->setUsername((string)$string);
		}
		self::$registeredMaintainers[$n->getUsername()] = $n;

		return self::$registeredMaintainers[$n->getUsername()];
	}


	/**
	 * @param $maintainer
	 *
	 * @return string
	 */
	public static function stringify($maintainer) {
		if ($maintainer instanceof Maintainer) {
			if ($maintainer->getUserId() == 0
			    && $maintainer->getUsername() == ''
			    || $maintainer->getUsername() == '(0)'
			) {
				return '';
			}
			if (!$maintainer->getUserId() && $maintainer->getUsername()) {
				return $maintainer->getUsername();
			}

			return "{$maintainer->getUsername()}({$maintainer->getUserId()})";
		}

		return $maintainer;
	}


	/**
	 * @param \League\Flysystem\Filesystem $filesystem
	 * @param string                       $path_to_file
	 */
	public static function writeMaintainerJson(Filesystem $filesystem, $path_to_file = 'Customizing/global/tools/maintainers/maintainers.json') {
		if (!$filesystem->has($path_to_file)) {
			$filesystem->write($path_to_file, '');
		}
		$maintainers = array();
		foreach (self::getRegisteredMaintainers() as $maintainer) {
			$maintainers[$maintainer->getUsername()] = self::stringify($maintainer);
		}

		$filesystem->update($path_to_file, JsonSerializable::json_encode($maintainers));
	}


	/**
	 * @param \League\Flysystem\Filesystem $filesystem
	 * @param string                       $path_to_file
	 */
	public static function loadMaintainerJson(Filesystem $filesystem, $path_to_file = 'Customizing/global/tools/maintainers/maintainers.json') {
		if (!$filesystem->has($path_to_file)) {
			$filesystem->write($path_to_file, '[]');
		}

		foreach (json_decode($filesystem->read($path_to_file)) as $k => $item) {
			$new = self::fromString($item);

			self::$registeredMaintainers[$new->getUsername()] = $new;
		}
	}


	/**
	 * @return \ILIAS\Tools\Maintainers\Maintainer[]
	 */
	public static function getRegisteredMaintainers() {
		return self::$registeredMaintainers;
	}


	/**
	 * @param \ILIAS\Tools\Maintainers\Maintainer[] $registeredMaintainers
	 */
	public static function setRegisteredMaintainers(array $registeredMaintainers) {
		self::$registeredMaintainers = $registeredMaintainers;
	}


	public function doPopulate() {
	}


	public function doStringyfy() {
	}
}