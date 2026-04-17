<?php

namespace Fab\Formule\Service;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Processor\ProcessorInterface;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * DataService
 */
class DataService
{
    /**
     * @var string
     */
    protected $templateIdentifier = '';

    /**
     * DataService constructor.
     *
     * @param string $templateIdentifier
     */
    public function __construct(string $templateIdentifier)
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

        $tableName = $this->getTemplateService()->getPersistingTable();
        $connection = $this->getConnection($tableName);
        $finalValues = $this->filterValuesToExistingTableColumns($connection, $tableName, $finalValues);
        $connection->insert(
            $tableName,
            $finalValues
        );

        $finalValues['uid'] = $connection->lastInsertId();
        return $finalValues;
    }

    /**
     * @param string $tableName
     * @return object|Connection
     */
    protected function getConnection(string $tableName): Connection
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $connectionPool->getConnectionForTable($tableName);
    }

    /**
     * @param array $values
     * @return array
     */
    public function update(array $values): array
    {

        // Finalize values
        $finalValues = array_merge(
            $this->getSanitizedValues($values),
            $this->getTimeStamp()
        );

        $finalValues = $this->processValues($finalValues, ProcessorInterface::UPDATE);
        unset($finalValues['token'], $finalValues['uid']);

        $tableName = $this->getTemplateService()->getPersistingTable();
        $connection = $this->getConnection($tableName);
        $finalValues = $this->filterValuesToExistingTableColumns($connection, $tableName, $finalValues);

        $connection->update(
            $tableName,
            $finalValues,
            [
                $this->getTemplateService()->getIdentifierField() => $this->getIdentifierValue()
            ]
        );

        return $finalValues;
    }

    /**
     * @param array $values
     * @param string $insertOrUpdate
     * @return array
     */
    protected function processValues(array $values, string $insertOrUpdate): array
    {
        // Possible processor
        foreach ($this->getTemplateService()->getProcessors() as $className) {

            /** @var ProcessorInterface $processor */
            $processor = GeneralUtility::makeInstance($className);
            $values = $processor->process($values, $insertOrUpdate);
        };

        return $values;
    }

    /**
     * @return bool
     */
    public function recordExists(): bool
    {
        $record = [];

        if ($this->hasIdentifierValue()) {

            $tableName = $this->getTemplateService()->getPersistingTable();

            /** @var QueryBuilder $query */
            $query = $this->getQueryBuilder($tableName);

            /** @var DeletedRestriction $restriction */
            $restriction = GeneralUtility::makeInstance(DeletedRestriction::class);
            $query
                ->getRestrictions()
                ->removeAll()
                ->add($restriction);

            $query->select('*')
                ->from($tableName)
                ->where(
                    $this->getClause()
                );

            $record = $query
                ->executeQuery()
                ->fetchAssociative();
        }

        return !empty($record);
    }

    /**
     * @return string
     */
    protected function getClause(): string
    {
        $tableName = $this->getTemplateService()->getPersistingTable();

        /** @var QueryBuilder $query */
        $query = $this->getQueryBuilder($tableName);

        return $query->expr()->eq(
            $this->getTemplateService()->getIdentifierField(),
            $query->expr()->literal($this->getIdentifierValue())
        );
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
    protected function getIdentifierValue(): string
    {
        $identifierField = $this->getTemplateService()->getIdentifierField();
        return (string)($GLOBALS['TYPO3_REQUEST']->getParsedBody()[$identifierField] ?? $GLOBALS['TYPO3_REQUEST']->getQueryParams()[$identifierField] ?? null);
    }

    /**
     * @param array $values
     * @return array
     */
    protected function getSanitizedValues(array $values): array
    {
        $tableName = $this->getTemplateService()->getPersistingTable();
        $sanitizedValues = [];
        foreach ($values as $fieldName => $value) {

            $resolvedField = $this->resolveField($fieldName);

            // Field must exist in the TCA.
            if (isset($GLOBALS['TCA'][$tableName]['columns'][$resolvedField])) {
                $sanitizedValues[$resolvedField] = $value;
            }
        }

        return $sanitizedValues;
    }

    /**
     * Keep only keys that exist as real columns on the table. TCA may define fields before the DB is migrated.
     *
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    protected function filterValuesToExistingTableColumns(Connection $connection, string $tableName, array $values): array
    {
        if ($tableName === '' || $values === []) {
            return $values;
        }

        $existingColumns = array_flip($connection->getSchemaInformation()->listTableColumnNames($tableName));
        $filtered = [];
        foreach ($values as $key => $value) {
            if (isset($existingColumns[$key])) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
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
    protected function getCreateTimeStamp(): array
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
    protected function resolveField(string $fieldName): string
    {
        $mappings = $this->getTemplateService()->getMappings();

        $resolvedFieldName = $fieldName;
        if (array_key_exists($fieldName, $mappings)) {
            $resolvedFieldName = $mappings[$fieldName];
        }

        return $resolvedFieldName;
    }

    /**
     * @param string $tableName
     * @return object|QueryBuilder
     */
    protected function getQueryBuilder(string $tableName): QueryBuilder
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $connectionPool->getQueryBuilderForTable($tableName);
    }

    /**
     * @return TemplateService|object
     */
    protected function getTemplateService()
    {
        return GeneralUtility::makeInstance(TemplateService::class, (int)$this->templateIdentifier);
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
     * @return \TYPO3\CMS\Core\Domain\Repository\PageRepository
     */
    protected function getPageRepository(): \TYPO3\CMS\Core\Domain\Repository\PageRepository
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Core\Domain\Repository\PageRepository::class);
    }

}
