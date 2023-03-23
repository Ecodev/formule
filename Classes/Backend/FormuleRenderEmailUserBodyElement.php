<?php
declare(strict_types = 1);
namespace Fab\Formule\Backend;

use Fab\Formule\Service\TemplateService;

class FormuleRenderEmailUserBodyElement extends AbstractFormuleElement
{
    public function render()
    {
        // Custom TCA properties and other data can be found in $this->data, for example the above
        // parameters are available in $this->data['parameterArray']['fieldConf']['config']['parameters']
        $result = $this->initializeResultArray();
        $parameters['row'] = $this->data['databaseRow'];

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
                if (empty($this->data['parameterArray']['itemFormElValue'])) {
                    $value = '{name},

We have received your request via the contact form on {HTTP_HOST}. We will process your request and get in contact with you soon.

<fo:form.show labelsIn="formule"/>

{namespace fo=Fab\Formule\ViewHelpers}';
                } else {
                    $value = $this->data['parameterArray']['itemFormElValue'];
                }

                $output = sprintf(
                    '<textarea name="%s" style="max-height: 500px; overflow: hidden; word-wrap: break-word; height: 300px;" class="form-control formengine-textarea" rows="10">%s</textarea>',
                    $this->data['parameterArray']['itemFormElName'],
                    $value
                );

            } else {
                $output = sprintf(
                    '<strong>%s</strong>',
                    $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:template.body')
                );
            }
        }


        $result['html'] = $output;
        return $result;
    }

}
