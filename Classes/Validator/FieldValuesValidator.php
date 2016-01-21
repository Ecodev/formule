<?php
namespace Fab\Formule\Validator;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validate the honey pot.
 */
class FieldValuesValidator extends AbstractValidator
{

    /**
     * @param array $values
     */
    public function isValid($values)
    {
        foreach ($this->getTemplateService()->getRequiredFields() as $requiredField) {
            if (empty($values[$requiredField])) {

                $value = LocalizationUtility::translate('error.required', 'formule');
                $this->addError(sprintf('%s "%s"', $value, $requiredField), 1452897562);
            }
        }
    }

    /**
     * @return TemplateService
     */
    protected function getTemplateService()
    {
        return GeneralUtility::makeInstance(TemplateService::class);
    }

}
