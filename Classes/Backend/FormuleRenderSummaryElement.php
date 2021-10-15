<?php
declare(strict_types = 1);
namespace Fab\Formule\Backend;

class FormuleRenderSummaryElement extends AbstractFormuleElement
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

        $result['html'] = $output;
        return $result;
    }

}