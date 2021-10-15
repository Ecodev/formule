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
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Instantiate additional validators coming from the TS configuration.
 */
class UserDefinedValidator extends AbstractValidator
{

    /**
     * @param array $values
     * @throws \InvalidArgumentException
     */
    public function isValid($values)
    {
        foreach ($this->getTemplateService()->getValidators() as $className) {

            /** @var \Fab\Formule\Validator\ValidatorInterface $validator */
            $validator = GeneralUtility::makeInstance($className);
            $messages = $validator->validate($values);

            if (is_array($messages)) {
                foreach ($messages as $fieldName => $message) {
                    $this->getValidationService()->addError($fieldName, $message);
                    $this->addError($message, 1453535466);
                }
            }
        }
    }

    /**
     * @return TemplateService
     * @throws \InvalidArgumentException
     */
    protected function getTemplateService()
    {
        return GeneralUtility::makeInstance(TemplateService::class);
    }

    /**
     * @return ValidationService
     * @throws \InvalidArgumentException
     */
    protected function getValidationService()
    {
        return GeneralUtility::makeInstance(ValidationService::class);
    }

}
