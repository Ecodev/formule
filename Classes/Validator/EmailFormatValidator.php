<?php
namespace Fab\Formule\Validator;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class EmailFormatValidator
 */
class EmailFormatValidator extends AbstractValidator
{

    /**
     * @param array $values
     * @return array
     */
    public function validate(array $values): array
    {

        $messages = [];
        if (filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
            $value = LocalizationUtility::translate('error.email.format', 'formule');
            $messages['email'] = $value;
        }

        return $messages;
    }

    /**
     * Validation method expected by Extbase/Formule framework
     *
     * @param mixed $value
     * @return bool
     */
    public function isValid($value): bool
    {
        // For compatibility with Extbase validator interface
        // Convert the value to array format expected by validate()
        $values = is_array($value) ? $value : ['email' => $value];

        $messages = $this->validate($values);

        // If there are validation messages, the validation failed
        return empty($messages);
    }
}
