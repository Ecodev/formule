<?php
namespace Fab\Formule\Service;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;

/**
 * TypoScriptService
 */
class TypoScriptService implements SingletonInterface
{

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Returns the TypoScript configuration for this extension.
     *
     * @return array
     */
    public function getSettings()
    {
        // Use cache or initialize settings property.
        if (empty($this->settings)) {

            if ($this->isFrontendMode()) {
                $this->settings = GeneralUtility::removeDotsFromTS($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_formule.']['settings.']);
            } else {

                $setup = $this->getConfigurationManager()->getTypoScriptSetup();
                if (is_array($setup['plugin.']['tx_formule.'])) {

                    /** @var \TYPO3\CMS\Core\TypoScript\TypoScriptService $typoScriptService */
                    $typoScriptService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\TypoScriptService::class);
                    $this->settings = $typoScriptService->convertTypoScriptArrayToPlainArray($setup['plugin.']['tx_formule.']['settings.']);
                }
            }
        }

        return $this->settings;
    }

    protected function getConfigurationManager(): BackendConfigurationManager
    {
        return GeneralUtility::makeInstance(BackendConfigurationManager::class);
    }

    protected function isFrontendMode(): bool
    {
        return ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend();
    }

}
