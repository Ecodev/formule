<?php
namespace Fab\Formule\Service;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
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
     * Public DI alias id, see Configuration/Services.yaml
     */
    private const BACKEND_CONFIGURATION_MANAGER_SERVICE_ID = 'fab.formule.backend_configuration_manager';

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Returns the TypoScript configuration for this extension.
     *
     * @return array
     */
    public function getSettings(): array
    {
        // Use cache or initialize settings property.
        if (empty($this->settings)) {

            if ($this->isFrontendMode()) {
                $this->settings = GeneralUtility::removeDotsFromTS($GLOBALS['TYPO3_REQUEST']->getAttribute('frontend.typoscript')->getSetupArray()['plugin.']['tx_formule.']['settings.']);
            } else {
                $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
                if ($request instanceof ServerRequestInterface) {
                    $setup = $this->resolveBackendConfigurationManager()->getTypoScriptSetup($request);
                    if (is_array($setup['plugin.']['tx_formule.'])) {

                        /** @var \TYPO3\CMS\Core\TypoScript\TypoScriptService $typoScriptService */
                        $typoScriptService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\TypoScript\TypoScriptService::class);
                        $this->settings = $typoScriptService->convertTypoScriptArrayToPlainArray($setup['plugin.']['tx_formule.']['settings.']);
                    }
                }
            }
        }

        return $this->settings;
    }

    protected function resolveBackendConfigurationManager(): BackendConfigurationManager
    {
        return GeneralUtility::getContainer()->get(self::BACKEND_CONFIGURATION_MANAGER_SERVICE_ID);
    }

    protected function isFrontendMode(): bool
    {
        return ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend();
    }

}
