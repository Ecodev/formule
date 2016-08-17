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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A class to interact with TCEForms.
 */
class TceForms
{

    /**
     * Render the field "NameFrom"
     *
     * @param array $parameters
     * @return string
     */
    public function renderNameFrom(array $parameters)
    {
        if (empty($parameters['row']['uid'])) {
            $output = sprintf(
                '<strong>%s</strong>',
                $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.missing.template')
            );
        } else {

            $value = '';
            if (!empty($parameters['itemFormElValue'])) {
                $value = $parameters['itemFormElValue'];
            } elseif (!empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'])) {
                $value = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'];
            }

            $output = sprintf(
                '<input type="text" name="%s" class="form-control t3js-clearable hasDefaultValue" value="%s" placeholder="%s"/>',
                $parameters['itemFormElName'],
                $value,
                $value === '' ? 'Consider giving a value for $GLOBALS[\'TYPO3_CONF_VARS\'][\'MAIL\'][\'defaultMailFromName\']' : ''
            );
        }

        return $output;
    }

    /**
     * Render the field "EmailFrom"
     *
     * @param array $parameters
     * @return string
     */
    public function renderEmailFrom(array $parameters)
    {
        if (empty($parameters['row']['uid'])) {
            $output = sprintf(
                '<strong>%s</strong>',
                $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.missing.template')
            );
        } else {

            $value = '';
            if (!empty($parameters['itemFormElValue'])) {
                $value = $parameters['itemFormElValue'];
            } elseif (!empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'])) {
                $value = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
            }

            $output = sprintf(
                '<input type="text" name="%s" class="form-control t3js-clearable hasDefaultValue" value="%s" placeholder="%s"/>',
                $parameters['itemFormElName'],
                $value,
                $value === '' ? 'Consider giving a value for $GLOBALS[\'TYPO3_CONF_VARS\'][\'MAIL\'][\'defaultMailFromAddress\']' : ''
            );
        }

        return $output;
    }

    /**
     * Render the field "emailAdminBody"
     *
     * @param array $parameters
     * @return string
     */
    public function renderFeedback(array $parameters)
    {
        $settings = $this->getSettings($parameters);
        $templateIdentifier = (int)$settings['template'];

        $output = sprintf(
            '<strong>%s</strong>',
            $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.missing.template')
        );

        if ($templateIdentifier > 0) {

            // Get template service
            $templateService = $this->getTemplateService($settings['template']);

            // find a possible feedback in the template.
            $body = $templateService->getSection(TemplateService::SECTION_FEEDBACK);

            if (empty($body)) {
                if (empty($parameters['itemFormElValue'])) {
                    $value = 'Thank you for your message. We will process your request and get in contact with you soon.

If this field is let blank section "feedback" of the template will be rendered instead!

<fo:form.show labelsIn="formule"/>
{namespace fo=Fab\Formule\ViewHelpers}';
                } else {
                    $value = $parameters['itemFormElValue'];
                }

                $output = sprintf(
                    '<textarea name="%s" style="max-height: 500px; overflow: hidden; word-wrap: break-word; height: 300px;" class="form-control formengine-textarea" rows="10">%s</textarea>',
                    $parameters['itemFormElName'],
                    $value
                );

            } else {
                $output = sprintf(
                    '<strong>%s</strong>',
                    $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:template.message')
                );
            }
        }

        return $output;
    }

    /**
     * Render the field "emailAdminBody"
     *
     * @param array $parameters
     * @return string
     */
    public function renderEmailUserBody(array $parameters)
    {
        $settings = $this->getSettings($parameters);
        $templateIdentifier = (int)$settings['template'];

        $output = sprintf(
            '<strong>%s</strong>',
            $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.missing.template')
        );

        if ($templateIdentifier > 0) {

            // Get template service
            $templateService = $this->getTemplateService($settings['template']);

            // find a possible body in the template.
            $body = $templateService->getSection(TemplateService::SECTION_EMAIL_USER);

            if (empty($body)) {
                if (empty($parameters['itemFormElValue'])) {
                    $value = '{name},

We have received your request via the contact form on {HTTP_HOST}. We will process your request and get in contact with you soon.

<fo:form.show labelsIn="formule"/>

{namespace fo=Fab\Formule\ViewHelpers}';
                } else {
                    $value = $parameters['itemFormElValue'];
                }

                $output = sprintf(
                    '<textarea name="%s" style="max-height: 500px; overflow: hidden; word-wrap: break-word; height: 300px;" class="form-control formengine-textarea" rows="10">%s</textarea>',
                    $parameters['itemFormElName'],
                    $value
                );

            } else {
                $output = sprintf(
                    '<strong>%s</strong>',
                    $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:template.body')
                );
            }
        }

        return $output;
    }

    /**
     * Render the field "emailAdminBody"
     *
     * @param array $parameters
     * @return string
     */
    public function renderEmailAdminBody(array $parameters)
    {
        $settings = $this->getSettings($parameters);
        $templateIdentifier = (int)$settings['template'];

        $output = sprintf(
            '<strong>%s</strong>',
            $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.missing.template')
        );

        if ($templateIdentifier > 0) {

            // Get template service
            $templateService = $this->getTemplateService($settings['template']);

            // find a possible body in the template.
            $body = $templateService->getSection(TemplateService::SECTION_EMAIL_ADMIN);

            if (empty($body)) {
                if (empty($parameters['itemFormElValue'])) {
                    $value = 'Hello Admin,

A user filled out the contact form on {HTTP_HOST} by {email}.

You **can** write content in your template using

* Markdown syntax
* Fluid syntax

Examples:

<f:translate key="email" extensionName="formule"/>: {email}

<f:link.page pageUid="1" absolute="1">Open page</f:link.page>

<fo:form.show labelsIn="formule"/>

{namespace fo=Fab\Formule\ViewHelpers}';
                } else {
                    $value = $parameters['itemFormElValue'];
                }

                $output = sprintf(
                    '<textarea name="%s" style="max-height: 500px; overflow: hidden; word-wrap: break-word; height: 300px;" class="form-control formengine-textarea" rows="10">%s</textarea>',
                    $parameters['itemFormElName'],
                    $value
                );

            } else {
                $output = sprintf(
                    '<strong>%s</strong>',
                    $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:template.body')
                );
            }
        }

        return $output;
    }

    /**
     * @param array $parameters
     * @return array
     */
    protected function getSettings(array $parameters)
    {

        // Get existing flexform configuration
        if (version_compare(TYPO3_branch, '7.0', '<')) {
            $flexform = $this->getLegacyFlexform($parameters);
        } else {
            $flexform = $parameters['row']['pi_flexform'];
        }

        $settings = $this->getFlexFormService()->extractSettings($flexform);
        if (is_array($settings['template'])) {
            $settings['template'] = current($settings['template']);
        }
        return $settings;
    }

    /**
     * Render the field "summary"
     *
     * @param array $parameters
     * @return string
     */
    public function renderSummary(array $parameters)
    {

        $settings = $this->getSettings($parameters);
        $templateIdentifier = (int)$settings['template'];

        $output = sprintf(
            '<strong>%s</strong>',
            $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.missing.template')
        );

        if ($templateIdentifier > 0) {

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

    <!-- template used -->
    <div class="box-summary">
        <div class="summary-title">%s</div>
        <div>%s</div>
    </div>

    <!-- extracted fields -->
    <div class="box-summary">
        <div class="summary-title">%s</div>
        <div>%s</div>
    </div>

    <!-- required fields -->
    <div class="box-summary">
        <div class="summary-title">%s</div>
        <div>%s</div>
    </div>

    <!-- spam protection -->
    <div class="box-summary">
        <div class="summary-title">%s</div>
        <div>%s</div>
    </div>

    <!-- persist table name -->
    <div class="box-summary">
        <div class="summary-title">%s</div>
        <div title="%s %s">%s</div>
    </div>

    <!-- possible warnings -->
    <div>
        <div><strong style="color: red">%s</strong></div>
        <div>%s</div>
    </div>
    ',
                $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.template.used'),
                $templateService->getPath(),
                $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.fields'),
                implode(', ', $templateService->getFields()),
                $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.mandatory.fields'),
                implode(', ', $templateService->getRequiredFields()),
                $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.spam.protection'),
                $templateService->hasHoneyPot() ?
                    $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.yes') :
                    $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.no'),
                $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.persisted.data'),
                $templateService->hasPersistingTable() ?
                    $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.persisted.to') :
                    '',
                $templateService->hasPersistingTable() ?
                    $templateService->getPersistingTable() :
                    '',
                $templateService->hasPersistingTable() ?
                    $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.yes') :
                    $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:summary.no'),
                $templateService->hasWarnings() ?
                    $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:warning') : '',
                $templateService->hasWarnings() ?
                    implode('<br>', $templateService->getWarnings()) : ''
            );
        }

        return $output;
    }

    /**
     * This method modifies the list of items for FlexForm "template".
     *
     * @param array $parameters
     * @throws \InvalidArgumentException
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
        return GeneralUtility::makeInstance(TemplateService::class, $templateIdentifier);
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