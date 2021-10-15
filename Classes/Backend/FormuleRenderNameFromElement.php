<?php
declare(strict_types = 1);
namespace Fab\Formule\Backend;


use Fab\Formule\Service\TemplateService;

class FormuleRenderNameFromElement extends AbstractFormuleElement
{
    public function render()
    {
        // Custom TCA properties and other data can be found in $this->data, for example the above
        // parameters are available in $this->data['parameterArray']['fieldConf']['config']['parameters']
        $result = $this->initializeResultArray();
        $parameters['row'] = $this->data['databaseRow'];

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

        $result['html'] = $output;
        return $result;
    }

}