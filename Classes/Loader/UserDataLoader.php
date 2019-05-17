<?php
namespace Fab\Formule\Loader;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
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
     * @return array
     */
    public function load(array $values): array
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
     * @return TemplateService|object
     */
    protected function getTemplateService()
    {
        return GeneralUtility::makeInstance(TemplateService::class);
    }
}
