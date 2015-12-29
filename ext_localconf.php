<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['formule']);

if (FALSE === isset($configuration['autoload_typoscript']) || TRUE === (bool)$configuration['autoload_typoscript']) {

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
		'formule',
		'constants',
		'<INCLUDE_TYPOSCRIPT: source="FILE:EXT:formule/Configuration/TypoScript/constants.ts">'
	);

	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
		'formule',
		'setup',
		'<INCLUDE_TYPOSCRIPT: source="FILE:EXT:formule/Configuration/TypoScript/setup.ts">'
	);
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Fab.formule',
	'Pi1',
	array(
		'Form' => 'show, submit',

	),
	// non-cacheable actions
	array(
		'Form' => 'show, submit',

	)
);
