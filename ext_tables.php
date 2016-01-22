<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'Fab.formule',
	'Pi1',
	'Formule'
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

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('vidi')) {

	/** @var \Fab\Vidi\Module\ModuleLoader $moduleLoader */
	$moduleLoader = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Fab\Vidi\Module\ModuleLoader::class, 'tx_formule_domain_model_sentmessage');

	/** @var \Fab\Vidi\Module\ModuleLoader $moduleLoader */
	$moduleLoader->setIcon('EXT:formule/Resources/Public/Images/tx_formule_domain_model_sentmessage.png')
		->setModuleLanguageFile('LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf')
		->setDefaultPid(0)
		->register();
}

// Add Flexform
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['formule_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
	'formule_pi1',
	sprintf('FILE:EXT:formule/Configuration/FlexForm/Formule.xml')
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['formule_pi1'] = 'layout, select_key, pages, recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['formule_pi1'] = 'pi_flexform';

$GLOBALS['TBE_MODULES_EXT']["xMOD_db_new_content_el"]['addElClasses']['tx_formule_wizard'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('formule') . 'Classes/Backend/Wizard.php';


# Declare token tables from the EM configuration.
$tableNames = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $configuration['tokenisable_tables']['value'], true);
foreach ($tableNames as $tableName) {
	\Fab\Formule\Token\TokenUtility::makeTokenisable('formule', $tableName);
}
