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

use Fab\Vidi\Domain\Model\Selection;
use Fab\Vidi\Facet\FacetInterface;
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
     * @var string
     */
    protected $gridConfigurationStandard = "<'row'<'col-xs-10'f><'col-xs-2 hidden-xs'l>r><'row'<'col-xs-12't>><'row'<'col-xs-6'i><'col-xs-6'p>>";
    /**
     * @var string
     */
    protected $gridConfigurationWithVisualBar = "<'row'<'col-sm-10 visual-search-container'><'col-xs-2 hidden-xs'l>r><'row'<'col-xs-12't>><'row'<'col-sm-4'i><'col-sm-4'f><'col-sm-4'p>>";

    /**
     * Render the field "isVisualSearchBarField"
     *
     * @param array $parameters
     * @return string
     */
    public function renderIsVisualSearchBarField(array $parameters)
    {

        // Get existing flexform configuration
        if (version_compare(TYPO3_branch, '7.0', '<')) {
            $flexform = $this->getLegacyFlexform($parameters);
        } else {
            $flexform = $parameters['row']['pi_flexform'];
        }

        $isVisualSearchBar = false;
        $normalizedFlexform = $this->normalizeFlexForm($flexform);
        if (!empty($normalizedFlexform['settings']) && $normalizedFlexform['settings']['isVisualSearchBar']) {
            $isVisualSearchBar = (bool)$normalizedFlexform['settings']['isVisualSearchBar'];
        }

        $output = sprintf(
            '<div class="t3-form-field t3-form-field-flex">
<input type="hidden" name="data[tt_content][%s][pi_flexform][data][facet][lDEF][settings.isVisualSearchBar][vDEF]" value="0"/>
<input type="checkbox" name="data[tt_content][%s][pi_flexform][data][facet][lDEF][settings.isVisualSearchBar][vDEF]" class="checkbox" %s value="1" id="isVisualSearchBar"/>
</div>

    <script>
    TYPO3.jQuery("#isVisualSearchBar").change(function() {

        var gridConfigurationStandard = "%s";
        var gridConfigurationWithVisualSearchBar = "%s";

        // Toggle grid configuration
        if (TYPO3.jQuery(this).is(":checked") && TYPO3.jQuery("#gridConfiguration").val() == gridConfigurationStandard) {
            TYPO3.jQuery("#gridConfiguration").val(gridConfigurationWithVisualSearchBar);
        } else if (TYPO3.jQuery("#gridConfiguration").val() == gridConfigurationWithVisualSearchBar) {
            TYPO3.jQuery("#gridConfiguration").val(gridConfigurationStandard);
        }
    });

    </script>
    ',
            $parameters['row']['uid'],
            $parameters['row']['uid'],
            $isVisualSearchBar ? 'checked="checked"' : '',
            $this->gridConfigurationStandard,
            $this->gridConfigurationWithVisualBar
        );


        return $output;
    }

    /**
     * Render the field "gridConfiguration"
     *
     * @param array $parameters
     * @return string
     */
    public function renderGridConfigurationField(array $parameters)
    {

        $output = '
<style>
.box-summary{
    margin-bottom: 10px;
}

.summary-title {
    font-weight: bold;
}
</style>

<div class="box-summary">
    <div class="summary-title">Template used</div>
    <div>EXT:foo/bar</div>
</div>
<div class="box-summary">
    <div class="summary-title">Fields detected</div>
    <div>name, firstName,</div>
</div>
<div class="box-summary">
    <div class="summary-title">Mandatory fields detected</div>
    <div>name, firstName,</div>
</div>

<div class="box-summary">
    <div class="summary-title">Persisted data</div>
    <div title="persisted to table fe_users">yes</div>
</div>
';


        return $output;
        // Default configuration
        $gridConfiguration = $this->gridConfigurationStandard;
        $helpText = sprintf(
            $this->getLanguageService()->sL('LLL:EXT:vidi_frontend/Resources/Private/Language/locallang.xlf:info.gridConfiguration'),
            '<a href="https://datatables.net/examples/basic_init/dom.html" target="_blank">DOM positioning</a>'
        );

        // Get existing flexform configuration
        if (version_compare(TYPO3_branch, '7.0', '<')) {
            $flexform = $this->getLegacyFlexform($parameters);
        } else {
            $flexform = $parameters['row']['pi_flexform'];
        }
        $normalizedFlexform = $this->normalizeFlexForm($flexform);
        if (!empty($normalizedFlexform['settings']) && $normalizedFlexform['settings']['gridConfiguration']) {
            $gridConfiguration = trim($normalizedFlexform['settings']['gridConfiguration']);
        }

        $output = sprintf(
            '<input name="data[tt_content][%s][pi_flexform][data][grid][lDEF][settings.gridConfiguration][vDEF]" id="gridConfiguration" style="width: 90%%" value="%s">
    <div>
    %s

    <ul>
    <li>l - Length changing</li>
    <li>f - Filtering input</li>
    <li>t - The table!</li>
    <li>i - Information</li>
    <li>p - Pagination</li>
    <li>r - processing</li>
    </ul>
    </div>
    ',
            $parameters['row']['uid'],
            $gridConfiguration,
            $helpText
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

    /**
     * This method modifies the list of items for FlexForm "facets".
     *
     * @param array $parameters
     */
    public function getFacets(&$parameters)
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

            if (!empty($configuredDataType)) {
                foreach (FrontendTca::grid($configuredDataType)->getFacetNames() as $facet) {
                    $values = array($facet, $facet, NULL);
                    if ($facet instanceof FacetInterface) {
                        $values = array($facet->getName(), $facet->getName(), NULL);
                    }
                    $parameters['items'][] = $values;
                }
            }
        }
    }

    /**
     * This method modifies the list of items for FlexForm "selection".
     *
     * @param array $parameters
     */
    public function getSelections(&$parameters)
    {
        $configuration = $this->getPluginConfiguration();

        if (empty($configuration) || empty($configuration['settings']['templates'])) {
            $parameters['items'][] = array('No template found. Forgotten to load the static TS template?', '', NULL);
        } else {

            $parameters['items'][] = array('', '', NULL);

            /** @var \Fab\Vidi\Domain\Repository\SelectionRepository $selectionRepository */
            $selectionRepository = $this->getObjectManager()->get('Fab\Vidi\Domain\Repository\SelectionRepository');

            if (version_compare(TYPO3_branch, '7.0', '<')) {
                $configuredDataType = $this->getDataTypeFromFlexformLegacy($parameters);
            } else {
                $configuredDataType = $this->getDataTypeFromFlexform($parameters['flexParentDatabaseRow']['pi_flexform']);
            }

            if ($configuredDataType) {

                $selections = $selectionRepository->findForEveryone($configuredDataType);

                if ($selections) {
                    foreach ($selections as $selection) {
                        /** @var Selection $selection */
                        $values = array($selection->getName(), $selection->getUid(), NULL);
                        $parameters['items'][] = $values;
                    }
                }
            }
        }
    }

    /**
     * This method modifies the list of items for FlexForm "sorting".
     *
     * @param array $parameters
     */
    public function getSorting(&$parameters)
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

            $parameters['items'][] = array('', '', NULL);
            if (!empty($configuredDataType)) {
                foreach (FrontendTca::grid($configuredDataType)->getFields() as $fieldNameAndPath => $configuration) {
                    if (FALSE === strpos($fieldNameAndPath, '__')) {
                        $values = array($fieldNameAndPath, $fieldNameAndPath, NULL);
                        $parameters['items'][] = $values;
                    }
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

            $normalizedFlexform = $this->normalizeFlexForm($flexform);
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
     * Parses the flexForm content and converts it to an array
     * The resulting array will be multi-dimensional, as a value "bla.blubb"
     * results in two levels, and a value "bla.blubb.bla" results in three levels.
     *
     * Note: multi-language flexForms are not supported yet
     *
     * @param array $flexForm flexForm xml string
     * @param string $languagePointer language pointer used in the flexForm
     * @param string $valuePointer value pointer used in the flexForm
     * @return array the processed array
     */
    protected function normalizeFlexForm(array $flexForm, $languagePointer = 'lDEF', $valuePointer = 'vDEF')
    {
        $settings = array();
        $flexForm = isset($flexForm['data']) ? $flexForm['data'] : array();
        foreach (array_values($flexForm) as $languages) {
            if (!is_array($languages[$languagePointer])) {
                continue;
            }
            foreach ($languages[$languagePointer] as $valueKey => $valueDefinition) {
                if (strpos($valueKey, '.') === false) {
                    $settings[$valueKey] = $this->walkFlexFormNode($valueDefinition, $valuePointer);
                } else {
                    $valueKeyParts = explode('.', $valueKey);
                    $currentNode = &$settings;
                    foreach ($valueKeyParts as $valueKeyPart) {
                        $currentNode = &$currentNode[$valueKeyPart];
                    }
                    if (is_array($valueDefinition)) {
                        if (array_key_exists($valuePointer, $valueDefinition)) {
                            $currentNode = $valueDefinition[$valuePointer];
                        } else {
                            $currentNode = $this->walkFlexFormNode($valueDefinition, $valuePointer);
                        }
                    } else {
                        $currentNode = $valueDefinition;
                    }
                }
            }
        }
        return $settings;
    }

    /**
     * Parses a flexForm node recursively and takes care of sections etc
     *
     * @param array $nodeArray The flexForm node to parse
     * @param string $valuePointer The valuePointer to use for value retrieval
     * @return array
     */
    protected function walkFlexFormNode($nodeArray, $valuePointer = 'vDEF')
    {
        if (is_array($nodeArray)) {
            $return = array();
            foreach ($nodeArray as $nodeKey => $nodeValue) {
                if ($nodeKey === $valuePointer) {
                    return $nodeValue;
                }
                if (in_array($nodeKey, array('el', '_arrayContainer'))) {
                    return $this->walkFlexFormNode($nodeValue, $valuePointer);
                }
                if ($nodeKey[0] === '_') {
                    continue;
                }
                if (strpos($nodeKey, '.')) {
                    $nodeKeyParts = explode('.', $nodeKey);
                    $currentNode = &$return;
                    $nodeKeyPartsCount = count($nodeKeyParts);
                    for ($i = 0; $i < $nodeKeyPartsCount - 1; $i++) {
                        $currentNode = &$currentNode[$nodeKeyParts[$i]];
                    }
                    $newNode = array(next($nodeKeyParts) => $nodeValue);
                    $currentNode = $this->walkFlexFormNode($newNode, $valuePointer);
                } elseif (is_array($nodeValue)) {
                    if (array_key_exists($valuePointer, $nodeValue)) {
                        $return[$nodeKey] = $nodeValue[$valuePointer];
                    } else {
                        $return[$nodeKey] = $this->walkFlexFormNode($nodeValue, $valuePointer);
                    }
                } else {
                    $return[$nodeKey] = $nodeValue;
                }
            }
            return $return;
        }
        return $nodeArray;
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
}