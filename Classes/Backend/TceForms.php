<?php
namespace Fab\Formule\Backend;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Fab\Formule\Service\FlexFormService;
use Fab\Formule\Service\TemplateService;
use Fab\Formule\Service\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A class to interact with TCEForms.
 */
class TceForms
{

    /**
     * Render the field "gridConfiguration"
     *
     * @param array $parameters
     * @return string
     */
    public function renderSummaryField(array $parameters)
    {

        // Get existing flexform configuration
        if (version_compare(TYPO3_branch, '7.0', '<')) {
            $flexform = $this->getLegacyFlexform($parameters);
        } else {
            $flexform = $parameters['row']['pi_flexform'];
        }

        $settings = $this->getFlexFormService()->extractSettings($flexform);
        $templateService = $this->getTemplateService($settings['template']);

        $output = sprintf(
            '
<style>
.box-summary{
    margin-bottom: 10px;
}

.summary-title {
    font-weight: bold;
}
</style>

<div class="box-summary">
    <div class="summary-title">%s</div>
    <div>%s</div>
</div>
<div class="box-summary">
    <div class="summary-title">%s</div>
    <div>%s</div>
</div>
<div class="box-summary">
    <div class="summary-title">%s</div>
    <div>%s</div>
</div>

<div class="box-summary">
    <div class="summary-title">%s</div>
    <div title="%s %s">%s</div>
</div>
',
            $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.template.used'),
            $templateService->getPath(),
            $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.fields'),
            implode(', ', $templateService->getFields()),
            $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.mandatory.fields'),
            implode(', ', $templateService->getRequiredFields()),
            $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.persisted.data'),
            $templateService->hasPersistingTable() ?
                $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.persisted.to') :
                '',
            $templateService->hasPersistingTable() ?
                $templateService->getPersistingTable() :
                '',
            $templateService->hasPersistingTable() ?
                $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.yes') :
                $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.no')
        );

        return $output;
    }

    /**
     * This method modifies the list of items for FlexForm "template".
     *
     * @param array $parameters
     */
    public function getTemplates(&$parameters)
    {
        $ts = $this->getTypoScriptService()->getSettings();

        if (empty($ts['templates'])) {
            $parameters['items'][] = array('No template found. Forgotten to load the static TS template?', '', NULL);
        } else {

            if (version_compare(TYPO3_branch, '7.0', '<')) {
                $configuredDataType = $this->getDataTypeFromFlexformLegacy($parameters);
            } else {
                $configuredDataType = $this->getDataTypeFromFlexform($parameters['flexParentDatabaseRow']['pi_flexform']);
            }

            $parameters['items'][] = ''; // Empty value
            foreach ($ts['templates'] as $key => $template) {
                $values = array($template['title'], $key, NULL);
                if (empty($template['dataType']) || $template['dataType'] === $configuredDataType) {
                    $parameters['items'][] = $values;
                }
            }
        }
    }

    /**
     * @param $parameters
     * @return string
     */
    protected function getDataTypeFromFlexformLegacy($parameters)
    {
        $configuredDataType = '';
        if (!empty($parameters['row']['pi_flexform'])) {
            $flexform = @GeneralUtility::xml2array($parameters['row']['pi_flexform']);
            if (!empty($flexform['data']['general']['lDEF']['settings.dataType'])) {
                $configuredDataType = $flexform['data']['general']['lDEF']['settings.dataType']['vDEF'];
            }
        }
        return $configuredDataType;
    }

    /**
     * @param array $flexform
     * @return string
     */
    protected function getDataTypeFromFlexform(array $flexform = array())
    {

        $configuredDataType = '';

        if (!empty($flexform)) {

            $normalizedFlexform = $this->getFlexFormService()->normalize($flexform);
            if (!empty($normalizedFlexform['settings']['dataType'])) {
                $configuredDataType = $normalizedFlexform['settings']['dataType'];
                if (is_array($configuredDataType)) {
                    $configuredDataType = $configuredDataType[0];
                }
            }
        }
        return $configuredDataType;
    }

    /**
     * Will be removed when dropping 6.2 compatibility.
     *
     * @param array $parameters
     * @return array
     */
    public function getLegacyFlexform(array $parameters)
    {
        $flexform = array();
        if ($parameters['row']['pi_flexform']) {
            $flexform = GeneralUtility::xml2array($parameters['row']['pi_flexform']);
        }

        return is_array($flexform) ? $flexform : [];
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return TypoScriptService
     */
    protected function getTypoScriptService()
    {
        return GeneralUtility::makeInstance(TypoScriptService::class);
    }

    /**
     * @param int $templateIdentifier
     * @return TemplateService
     */
    protected function getTemplateService($templateIdentifier)
    {
        return GeneralUtility::makeInstance(TemplateService::class, $templateIdentifier);
    }

    /**
     * @return FlexFormService
     */
    protected function getFlexFormService()
    {
        return GeneralUtility::makeInstance(FlexFormService::class);
    }

}