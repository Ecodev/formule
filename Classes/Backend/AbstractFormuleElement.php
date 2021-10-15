<?php
declare(strict_types = 1);
namespace Fab\Formule\Backend;

use Fab\Formule\Service\FlexFormService;
use Fab\Formule\Service\TemplateService;
use Fab\Formule\Service\TypoScriptService;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractFormuleElement extends AbstractFormElement
{
    /**
     * This method modifies the list of items for FlexForm "template".
     *
     * @param array $parameters
     * @throws \InvalidArgumentException
     */
    protected function getTemplates(&$parameters)
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
     * @param int $templateIdentifier
     * @return TemplateService
     * @throws \InvalidArgumentException
     */
    protected function getTemplateService($templateIdentifier)
    {
        return GeneralUtility::makeInstance(TemplateService::class, $templateIdentifier);
    }

    /**
     * @param array $parameters
     * @return array
     */
    protected function getSettings(array $parameters)
    {
        $flexform = $parameters['row']['pi_flexform'];

        $settings = $this->getFlexFormService()->extractSettings($flexform);
        if (is_array($settings['template'])) {
            $settings['template'] = current($settings['template']);
        }
        return $settings;
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
     * @return FlexFormService
     * @throws \InvalidArgumentException
     */
    protected function getFlexFormService()
    {
        return GeneralUtility::makeInstance(FlexFormService::class);
    }

}