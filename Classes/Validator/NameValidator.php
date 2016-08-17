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
 * Class NameValidator
 */
class NameValidator extends AbstractValidator
{

    /**
     * @param array $values
     * @return array
     */
    public function validate(array $values)
    {

        $messages = [];
        if ($values['first_name'] === $values['last_name']) {
            $value = LocalizationUtility::translate('error.name.identical', 'formule');
            $messages['first_name'] = $value;
            $messages['last_name'] = $value;
        }

        return $messages;
    }

}
