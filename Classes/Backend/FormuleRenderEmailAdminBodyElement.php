<?php
declare(strict_types = 1);
namespace Fab\Formule\Backend;


use Fab\Formule\Service\TemplateService;

class FormuleRenderEmailAdminBodyElement extends AbstractFormuleElement
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
            $body = $templateService->getSection(TemplateService::SECTION_EMAIL_ADMIN);

            if (empty($body)) {
                if (empty($this->data['parameterArray']['itemFormElValue'])) {
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
