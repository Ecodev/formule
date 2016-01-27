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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class EmailUniqueValidator
 */
class EmailUniqueValidator extends AbstractValidator
{

    /**
     * @param array $values
     * @return array
     */
    public function validate(array $values)
    {
        $messages = [];

        $tableName = $this->getTemplateService()->getPersistingTable();

        $clause = sprintf('email = "%s"', $this->getDatabaseConnection()->quoteStr($values['email'], $tableName));

        // true means we are updating the record.
        $identifierField = $this->getTemplateService()->getIdentifierField();
        $identifierValue = GeneralUtility::_GP($identifierField);

        if (!empty($identifierValue)) {
            $clause .= sprintf(
                ' AND %s != "%s"',
                $identifierField,
                $this->getDatabaseConnection()->quoteStr($identifierValue, $tableName)
            );
        }

        #$clause .= $this->getPageRepository()->enableFields($tableName);
        $clause .= $this->getPageRepository()->deleteClause($tableName);
        $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', $tableName, $clause);

        if (!empty($record)) {
            $value = LocalizationUtility::translate('error.email.unique', 'formule');
            $messages['email'] = $value;
        }

        return $messages;
    }


}
