<?php
namespace Fab\Formule\Service;

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

use Fab\Formule\Processor\ProcessorInterface;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * DataService
 */
class DataService
{

    /**
     * DataService constructor.
     *
     * @param string $templateIdentifier
     */
    public function __construct($templateIdentifier)
    {
        $this->templateIdentifier = $templateIdentifier;
    }

    /**
     * @param array $values
     * @return array
     */
    public function create(array $values)
    {
        // Finalize values
        $finalValues = array_merge(
            $this->getSanitizedValues($values),
            $this->getTemplateService()->getDefaultValues(),
            $this->getTimeStamp(),
            $this->getCreateTimeStamp()
        );

        $finalValues = $this->processValues($finalValues, ProcessorInterface::INSERT);

        $result = $this->getDatabaseConnection()->exec_INSERTquery($this->getTemplateService()->getPersistingTable(), $finalValues);

        if (!$result) {
            $this->getLogger()->error('Formule: I could not create a new ' . $this->getTemplateService()->getPersistingTable(), [
                $this->getDatabaseConnection()->INSERTquery($this->getTemplateService()->getPersistingTable(), $finalValues)
            ]);
        }

        $finalValues['uid'] = $this->getDatabaseConnection()->sql_insert_id();
        return $finalValues;
    }

    /**
     * @param array $values
     * @return array
     */
    public function update(array $values)
    {

        // Finalize values
        $finalValues = array_merge(
            $this->getSanitizedValues($values),
            $this->getTimeStamp()
        );

        $finalValues = $this->processValues($finalValues, ProcessorInterface::UPDATE);

        $result = $this->getDatabaseConnection()->exec_UPDATEquery(
            $this->getTemplateService()->getPersistingTable(),
            $this->getClause(),
            $finalValues
        );

        if (!$result) {
            $this->getLogger()->error('Formule: I could not update ' . $this->getTemplateService()->getPersistingTable() . ':' . $this->getTemplateService()->getIdentifierField(), [
                $result = $this->getDatabaseConnection()->UPDATEquery($this->getTemplateService()->getPersistingTable(), $clause, $finalValues)
            ]);
        }

        return $finalValues;
    }

    /**
     * @param array $values
     * @param string $insertOrUpdate
     * @return array
     */
    protected function processValues(array $values, $insertOrUpdate)
    {
        // Possible processor
        foreach ($this->getTemplateService()->getProcessors() as $className) {

            /** @var ProcessorInterface $processor */
            $processor = GeneralUtility::makeInstance($className);
            $values = array_merge($values, $processor->process($values, $insertOrUpdate));
        };

        return $values;
    }

    /**
     * @return bool
     */
    public function recordExists()
    {
        $record = [];

        if ($this->hasIdentifierValue()) {

            $tableName = $this->getTemplateService()->getPersistingTable();

            $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', $tableName, $this->getClause());
        }

        return !empty($record);
    }

    /**
     * @return string
     */
    protected function getClause()
    {
        $tableName = $this->getTemplateService()->getPersistingTable();
        $clause = sprintf(
            '%s = "%s"',
            $this->getTemplateService()->getIdentifierField(),
            $this->getDatabaseConnection()->quoteStr($this->getIdentifierValue(), $tableName)
        );
        $clause .= $this->getPageRepository()->deleteClause($tableName);
        return $clause;
    }

    /**
     * @return string
     */
    protected function hasIdentifierValue()
    {
        return (bool)$this->getIdentifierValue();
    }

    /**
     * @return string
     */
    protected function getIdentifierValue()
    {
        $identifierField = $this->getTemplateService()->getIdentifierField();
        return (string)GeneralUtility::_GP($identifierField);
    }

    /**
     * @param array $values
     * @return array
     */
    protected function getSanitizedValues(array $values)
    {
        $tableName = $this->getTemplateService()->getPersistingTable();
        $sanitizedValues = [];
        foreach ($values as $fieldName => $value) {

            $resolvedField = $this->resolveField($fieldName);
            $sanitizedValues[$resolvedField] = $this->getDatabaseConnection()->quoteStr($value, $tableName);
        }

        return $sanitizedValues;
    }

    /**
     * @return array
     */
    protected function getTimeStamp()
    {
        $systemValues = [];
        $tableName = $this->getTemplateService()->getPersistingTableName();
        if (isset($GLOBALS['TCA'][$tableName]['ctrl']['tstamp'])) {
            $systemValues[$GLOBALS['TCA'][$tableName]['ctrl']['tstamp']] = time();
        }

        return $systemValues;
    }

    /**
     * @return array
     */
    protected function getCreateTimeStamp()
    {
        $systemValues = [];
        $tableName = $this->getTemplateService()->getPersistingTableName();
        if (isset($GLOBALS['TCA'][$tableName]['ctrl']['crdate'])) {
            $systemValues[$GLOBALS['TCA'][$tableName]['ctrl']['crdate']] = time();
        }

        return $systemValues;
    }

    /**
     * Returns a pointer to the database.
     *
     * @param string $fieldName
     * @return string
     */
    protected function resolveField($fieldName)
    {
        $mappings = $this->getTemplateService()->getMappings();

        $resolvedFieldName = $fieldName;
        if (array_key_exists($fieldName, $mappings)) {
            $resolvedFieldName = $mappings[$fieldName];
        }

        return $resolvedFieldName;
    }

    /**
     * Returns a pointer to the database.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * @return TemplateService
     */
    protected function getTemplateService()
    {
        return GeneralUtility::makeInstance(TemplateService::class, $this->templateIdentifier);
    }

    /**
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    protected function getLogger()
    {

        /** @var $loggerManager LogManager */
        $loggerManager = GeneralUtility::makeInstance(LogManager::class);

        /** @var $logger \TYPO3\CMS\Core\Log\Logger */
        return $loggerManager->getLogger(get_class($this));
    }

    /**
     * Returns an instance of the page repository.
     *
     * @return \TYPO3\CMS\Frontend\Page\PageRepository
     */
    protected function getPageRepository()
    {
        return $GLOBALS['TSFE']->sys_page;
    }
}