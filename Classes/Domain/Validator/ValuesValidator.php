<?php
namespace Fab\Formule\Domain\Validator;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Service\TemplateService;
use Fab\Formule\Service\ValidationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validate the honey pot.
 */
class ValuesValidator extends AbstractValidator
{

    /**
     * @param array $values
     * @throws \InvalidArgumentException
     */
    public function isValid($values)
    {
        foreach ($this->getTemplateService()->getRequiredFields() as $requiredField) {
            $value = trim($values[$requiredField]);
            if ((string)$value === '') {

                $message = LocalizationUtility::translate('error.required', 'formule');
                $this->getValidationService()->addError($requiredField, $message);
                $this->addError(sprintf('%s %s How to retrieve label???', $message, $requiredField), 1452897562);
            }
        }
    }

    /**
     * @return TemplateService|object
     * @throws \InvalidArgumentException
     */
    protected function getTemplateService()
    {
        return GeneralUtility::makeInstance(TemplateService::class);
    }

    /**
     * @return ValidationService|object
     * @throws \InvalidArgumentException
     */
    protected function getValidationService()
    {
        return GeneralUtility::makeInstance(ValidationService::class);
    }

}
