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
    public function validate(array $values)
    {

        $messages = [];
        if (filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
            $value = LocalizationUtility::translate('error.email.format', 'formule');
            $messages['email'] = $value;
        }

        return $messages;
    }

}
