<?php
declare(strict_types = 1);
namespace Fab\Formule\Backend;

use Fab\Formule\Service\TemplateService;

class FormuleRenderFeedbackElement extends AbstractFormuleElement
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

            // find a possible feedback in the template.
            $body = $templateService->getSection(TemplateService::SECTION_FEEDBACK);

            if (empty($body)) {
                if (empty($this->data['parameterArray']['itemFormElValue'])) {
                    $value = 'Thank you for your message. We will process your request and get in contact with you soon.

If this field is let blank section "feedback" of the template will be rendered instead!

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
                    $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:template.message')
                );
            }
        }

        $result['html'] = $output;
        return $result;
    }

}
