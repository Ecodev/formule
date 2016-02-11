<?php
namespace Fab\Formule\Loader;

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

/**
 * Class UserDataLoader
 * Only an example class to be copy / pasted and adjusted!!!
 */
class UserDataLoader extends AbstractLoader
{

    /**
     * @param array $values
     * @param string $insertOrUpdate
     * @return array
     */
    public function load(array $values, $insertOrUpdate = '')
    {

        $identifierField = $this->getTemplateService()->getIdentifierField();
        $identifierValue = GeneralUtility::_GP($identifierField);

        $tableName = $this->getTemplateService()->getPersistingTableName();
        $clause = sprintf(
            '%s = "%s"',
            $identifierField,
            $this->getDatabaseConnection()->quoteStr($identifierValue, $tableName)
        );

        $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', $tableName, $clause);

        $fields = $this->getTemplateService()->getFields();

        // is mapping necessary here?
        foreach ($fields as $field) {
            if (isset($record[$field])) {
                $values[$field] = $record[$field];
            }
        }

        $values['uid'] = $record['uid'];
        return $values;
    }

    /**
     * @return TemplateService
     */
    protected function getTemplateService()
    {
        return GeneralUtility::makeInstance(TemplateService::class);
    }
}
