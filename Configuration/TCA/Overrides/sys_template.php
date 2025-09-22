<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

$configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
)->get('formule');

// Possible Static TS loading
if (TRUE === isset($configuration['autoload_typoscript']) && FALSE === (bool)$configuration['autoload_typoscript']) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
        'formule',
        'Configuration/TypoScript',
        'Variety of forms - effortless!'
    );
}
