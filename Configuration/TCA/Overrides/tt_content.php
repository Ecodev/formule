<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Fab.formule',
    'Pi1',
    'Formule'
);

// Add Flexform
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['formule_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'formule_pi1',
    sprintf('FILE:EXT:formule/Configuration/FlexForm/Formule.xml')
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['formule_pi1'] = 'layout, select_key, pages, recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['formule_pi1'] = 'pi_flexform';
