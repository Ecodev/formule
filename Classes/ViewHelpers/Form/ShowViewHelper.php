<?php
namespace Fab\Formule\ViewHelpers\Form;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Service\TemplateService;
use FluidTYPO3\Vhs\Asset;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper to show sent values of a form.
 */
class ShowViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('labelsIn', 'string', '', false, 'formule');
        $this->registerArgument('labelPrefix', 'string', '', false, '');
        $this->registerArgument('excludedFields', 'string', '', false, '');
    }

    /**
     * Show sent values of a form.
     *
     * @return string
     */
    public function render()
    {
        $labelsIn = $this->arguments['labelsIn'];
        $labelPrefix = $this->arguments['labelPrefix'];
        $excludedFields = $this->arguments['excludedFields'];
        $output = '';

        $values = $this->templateVariableContainer->getAll();

        $excludedFields = GeneralUtility::trimExplode(',', $excludedFields, true);

        $allowedFields = $this->getTemplateService($values['templateIdentifier'])->getFields();

        if (!empty($values)) {

            $_values = [];
            foreach ($values as $key => $value) {

                if (in_array($key, $allowedFields) && !in_array($key, $excludedFields)) {

                    $_values[] = sprintf('
    <tr>
    <td style="vertical-align: top">%s</td>
    <td style="vertical-align: top">%s</td>
    </tr>
                    ',
                        $this->getLabel($key, $labelsIn, $labelPrefix),
                        $value
                    );
                }

            }

            // Assemble output
            $output = '<table class="table table-formule">';
            $output .= implode('', $_values);
            $output .= '</table>';
        }

        return $output;
    }

    /**
     * @param string $key
     * @param $labelsIn
     * @param $labelPrefix
     * @return string
     */
    protected function getLabel($key, $labelsIn, $labelPrefix)
    {
        $label = LocalizationUtility::translate($labelPrefix . $key, $labelsIn);
        if (empty($label)) {
            $label = $key;
        }
        return $label;
    }

    /**
     * @param int $templateIdentifier
     * @return object|TemplateService
     */
    protected function getTemplateService($templateIdentifier)
    {
        return GeneralUtility::makeInstance(TemplateService::class, $templateIdentifier);
    }

}
