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
