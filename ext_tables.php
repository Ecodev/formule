<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'Fab.formule',
	'Pi1',
	'Pi1'
);

/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);

/** @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility */
$configurationUtility = $objectManager->get(\TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility::class);
$configuration = $configurationUtility->getCurrentConfiguration('formule');


// Possible Static TS loading
if (TRUE === isset($configuration['autoload_typoscript']['value']) && FALSE === (bool)$configuration['autoload_typoscript']['value']) {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('formule', 'Configuration/TypoScript', 'Variety of forms - effortless!');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_formule_domain_model_sentmessage');
