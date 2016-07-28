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
        'Form' => 'show, submit, feedback',

    ),
    // non-cacheable actions
    array(
        'Form' => 'show, submit, feedback',

    )
);

// Duplicate feature of EXT:messenger
if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('messenger')) {

    // Override classes for the Object Manager
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\CMS\Core\Mail\MailMessage'] = array(
        'className' => 'Fab\Formule\Override\Core\Mail\MailMessage'
    );

    # Install PSR-0-compatible class autoloader for Markdown Library in Resources/PHP/Michelf
    spl_autoload_register(function ($class) {
        if (strpos($class, 'Michelf\Markdown') !== FALSE) {
            require sprintf('%sResources/Private/PHP/Markdown/%s',
                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('formule'),
                preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, ltrim($class, '\\')) . '.php'
            );
        }
    });
}
