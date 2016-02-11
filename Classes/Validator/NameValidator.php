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
