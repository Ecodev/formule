<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

/** @var \TYPO3\CMS\Core\Package\PackageManager $packageManager */
$packageManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Package\PackageManager::class);

if ($packageManager->isPackageActive('vidi')
    && !$packageManager->isPackageActive('messenger')
) {

    /** @var \Fab\Vidi\Module\ModuleLoader $moduleLoader */
    $moduleLoader = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Fab\Vidi\Module\ModuleLoader::class, 'tx_formule_domain_model_sentmessage');

    $moduleLoader->setIcon('EXT:formule/Resources/Public/Images/tx_formule_domain_model_sentmessage.png')
        ->setModuleLanguageFile('LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf')
        ->setDefaultPid(0)
        ->register();
}
