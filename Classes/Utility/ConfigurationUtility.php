<?php
namespace Fab\Formule\Utility;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * A class for handling configuration of the extension
 */
class ConfigurationUtility implements SingletonInterface {

	/**
	 * @var array
	 */
	protected $configuration = array();

	/**
	 * Returns a class instance.
	 *
	 * @return \Fab\Formule\Utility\ConfigurationUtility|object
	 */
	static public function getInstance() {
		return GeneralUtility::makeInstance(self::class);
	}

	/**
	 * Constructor
     */
	public function __construct() {


        $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
        )->get('formule');

		// Fill up configuration array with relevant values.
		foreach ($configuration as $key => $value) {
			$this->configuration[$key] = $value;
		}
	}

	/**
	 * @return ObjectManager|object
	 */
	protected function getObjectManager() {
		return GeneralUtility::makeInstance(ObjectManager::class);
	}

	/**
	 * Returns a setting key.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		return isset($this->configuration[$key]) ? trim($this->configuration[$key]) : NULL;
	}

	/**
	 * Set a setting key.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function set($key, $value) {
		$this->configuration[$key] = $value;
	}

	/**
	 * @return array
	 */
	public function getConfiguration() {
		return $this->configuration;
	}

}
