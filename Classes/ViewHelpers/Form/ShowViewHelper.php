<?php
namespace Fab\Formule\ViewHelpers\Form;

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
     * Show sent values of a form.
     *
     * @param string $labelsIn
     * @param string $labelPrefix
     * @param string $excludedFields
     * @return string
     */
    public function render($labelsIn = 'formule', $labelPrefix = '', $excludedFields = '')
    {

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
     * @return TemplateService
     */
    protected function getTemplateService($templateIdentifier)
    {
        return GeneralUtility::makeInstance(TemplateService::class, $templateIdentifier);
    }

}
