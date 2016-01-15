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
use Fab\Formule\Service\TemplateAnalyserService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\Tca\Tca;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\TypoScriptService;

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
    public function renderGridConfigurationField(array $parameters)
    {

        // Get existing flexform configuration
        if (version_compare(TYPO3_branch, '7.0', '<')) {
            $flexform = $this->getLegacyFlexform($parameters);
        } else {
            $flexform = $parameters['row']['pi_flexform'];
        }

        $settings = $this->getFlexFormService()->extractSettings($flexform);

        $templateAnalyser = $this->getTemplateAnalyserService($settings['template']);


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
    <div title="%s xxx">%s</div>
</div>
',
            $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.template.used'),
            $settings['template'],
            $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.fields'),
            implode(', ', $templateAnalyser->getFields()),
            $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.mandatory.fields'),
            implode(', ', $templateAnalyser->getRequiredFields()),
            $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.persisted.data'),
            $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.persisted.to'),
            $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.no')
        );


        return $output;
    }

    /**
     * This method modifies the list of items for FlexForm "dataType".
     *
     * @param array $parameters
     */
    public function getDataTypes(&$parameters)
    {

        /** @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility */
        $configurationUtility = $this->getObjectManager()->get('TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility');
        $configuration = $configurationUtility->getCurrentConfiguration('vidi_frontend');
        $availableContentTypes = GeneralUtility::trimExplode(',', $configuration['content_types']['value'], TRUE);

        foreach ($GLOBALS['TCA'] as $contentType => $tca) {
            if (isset($GLOBALS['TCA'][$contentType]['grid']) && (empty($availableContentTypes) || in_array($contentType, $availableContentTypes))) {
                $label = sprintf(
                    '%s (%s)',
                    Tca::table($contentType)->getTitle(),
                    $contentType
                );
                $values = array($label, $contentType, NULL);

                $parameters['items'][] = $values;
            }
        }
    }

    /**
     * This method modifies the list of items for FlexForm "template".
     *
     * @param array $parameters
     */
    public function getTemplates(&$parameters)
    {
        $configuration = $this->getPluginConfiguration();

        if (empty($configuration) || empty($configuration['settings']['templates'])) {
            $parameters['items'][] = array('No template found. Forgotten to load the static TS template?', '', NULL);
        } else {

            if (version_compare(TYPO3_branch, '7.0', '<')) {
                $configuredDataType = $this->getDataTypeFromFlexformLegacy($parameters);
            } else {
                $configuredDataType = $this->getDataTypeFromFlexform($parameters['flexParentDatabaseRow']['pi_flexform']);
            }

            $parameters['items'][] = ''; // Empty value
            foreach ($configuration['settings']['templates'] as $template) {
                $values = array($template['title'], $template['path'], NULL);
                if (empty($template['dataType']) || $template['dataType'] === $configuredDataType) {
                    $parameters['items'][] = $values;
                }
            }
        }
    }

    /**
     * This method modifies the list of items for FlexForm "template".
     *
     * @param array $parameters
     */
    public function getColumns(&$parameters)
    {

        $configuration = $this->getPluginConfiguration();

        if (empty($configuration) || empty($configuration['settings']['templates'])) {
            $parameters['items'][] = array('No template found. Forgotten to load the static TS template?', '', NULL);
        } else {


            if (version_compare(TYPO3_branch, '7.0', '<')) {
                $configuredDataType = $this->getDataTypeFromFlexformLegacy($parameters);
            } else {
                $configuredDataType = $this->getDataTypeFromFlexform($parameters['flexParentDatabaseRow']['pi_flexform']);
            }

            if (empty($configuredDataType)) {
                $parameters['items'][] = array('No columns to display yet! Save this record.', '', NULL);
            } else {
                foreach (FrontendTca::grid($configuredDataType)->getFields() as $fieldNameAndPath => $configuration) {
                    $values = array($fieldNameAndPath, $fieldNameAndPath, NULL);
                    $parameters['items'][] = $values;
                }
            }
        }
    }

//    /**
//     * This method modifies the list of items for FlexForm "selection".
//     *
//     * @param array $parameters
//     */
//    public function getSelections(&$parameters)
//    {
//        $configuration = $this->getPluginConfiguration();
//
//        if (empty($configuration) || empty($configuration['settings']['templates'])) {
//            $parameters['items'][] = array('No template found. Forgotten to load the static TS template?', '', NULL);
//        } else {
//
//            $parameters['items'][] = array('', '', NULL);
//
//            /** @var \Fab\Vidi\Domain\Repository\SelectionRepository $selectionRepository */
//            $selectionRepository = $this->getObjectManager()->get('Fab\Vidi\Domain\Repository\SelectionRepository');
//
//            if (version_compare(TYPO3_branch, '7.0', '<')) {
//                $configuredDataType = $this->getDataTypeFromFlexformLegacy($parameters);
//            } else {
//                $configuredDataType = $this->getDataTypeFromFlexform($parameters['flexParentDatabaseRow']['pi_flexform']);
//            }
//
//            if ($configuredDataType) {
//
//                $selections = $selectionRepository->findForEveryone($configuredDataType);
//
//                if ($selections) {
//                    foreach ($selections as $selection) {
//                        /** @var Selection $selection */
//                        $values = array($selection->getName(), $selection->getUid(), NULL);
//                        $parameters['items'][] = $values;
//                    }
//                }
//            }
//        }
//    }

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
     * Returns the TypoScript configuration for this extension.
     *
     * @return array
     */
    protected function getPluginConfiguration()
    {
        $setup = $this->getConfigurationManager()->getTypoScriptSetup();

        $pluginConfiguration = array();
        if (is_array($setup['plugin.']['tx_formule.'])) {
            /** @var TypoScriptService $typoScriptService */
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $pluginConfiguration = $typoScriptService->convertTypoScriptArrayToPlainArray($setup['plugin.']['tx_formule.']);
        }
        return $pluginConfiguration;
    }

    /**
     * @return BackendConfigurationManager
     */
    protected function getConfigurationManager()
    {
        return $this->getObjectManager()->get(BackendConfigurationManager::class);
    }

    /**
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        /** @var ObjectManager $objectManager */
        return GeneralUtility::makeInstance(ObjectManager::class);
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
     * @return TemplateAnalyserService
     */
    protected function getTemplateAnalyserService($templateNameAndPath)
    {
        return GeneralUtility::makeInstance(TemplateAnalyserService::class, $templateNameAndPath);
    }

    /**
     * @return FlexFormService
     */
    protected function getFlexFormService()
    {
        return GeneralUtility::makeInstance(FlexFormService::class);
    }

}