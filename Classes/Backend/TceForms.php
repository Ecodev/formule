<?php
namespace Fab\Formule\Backend;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Service\FlexFormService;
use Fab\Formule\Service\TemplateService;
use Fab\Formule\Service\TypoScriptService;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A class to interact with TCEForms.
 */
class TceForms
{

    /**
     * @param array $parameters
     * @return array
     */
    protected function getSettings(array $parameters)
    {
        // Get existing flexform configuration
        $flexform = $parameters['row']['pi_flexform'];

        $settings = $this->getFlexFormService()->extractSettings($flexform);
        if (is_array($settings['template'])) {
            $settings['template'] = current($settings['template']);
        }
        return $settings;
    }

    /**
     * This method modifies the list of items for FlexForm "template".
     *
     * @param array $parameters
     * @throws \InvalidArgumentException
     */
    public function getTemplates(&$parameters): void
    {
        $ts = $this->getTypoScriptService()->getSettings();

        if (empty($ts['templates'])) {
            $parameters['items'][] = array('No template found. Forgotten to load the static TS template?', '', NULL);
        } else {

            $configuredDataType = $this->getDataTypeFromFlexform($parameters['flexParentDatabaseRow']['pi_flexform']);

            $parameters['items'][] = ''; // Empty value
            foreach ($ts['templates'] as $key => $template) {
                $templateTitle = $this->getLanguageService()->sL($template['title']);

                if (empty($templateTitle)) {
                    $templateTitle = $template['title'];
                }
                $values = array($templateTitle, $key, NULL);
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
     * @throws \InvalidArgumentException
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
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return TypoScriptService
     * @throws \InvalidArgumentException
     */
    protected function getTypoScriptService()
    {
        return GeneralUtility::makeInstance(TypoScriptService::class);
    }

    /**
     * @param int $templateIdentifier
     * @return TemplateService
     * @throws \InvalidArgumentException
     */
    protected function getTemplateService($templateIdentifier)
    {
        return GeneralUtility::makeInstance(TemplateService::class, (int)$templateIdentifier);
    }

    /**
     * @return FlexFormService
     * @throws \InvalidArgumentException
     */
    protected function getFlexFormService()
    {
        return GeneralUtility::makeInstance(FlexFormService::class);
    }

}
