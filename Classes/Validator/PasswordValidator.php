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

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class PasswordValidator
 */
class PasswordValidator extends AbstractValidator
{

    /**
     * @param array $values
     * @return array
     */
    public function validate(array $values)
    {

        $messages = [];
        if ($values['password'] !== $values['password_confirmation']) {
            $value = LocalizationUtility::translate('error.password.not.corresponding', 'formule');
            $messages['password'] = $value;
        }

        if (!empty($values['password']) && strlen($values['password']) < 8) {
            $value = LocalizationUtility::translate('error.password.too.short', 'formule');

            if (isset($messages['password'])) {
                $messages['password']= $messages['password'] . ' ' .$value;
            } else {
                $messages['password']= $value;
            }

        }

        return $messages;
    }


}
