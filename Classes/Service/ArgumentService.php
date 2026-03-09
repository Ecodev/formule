<?php
namespace Fab\Formule\Service;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ArgumentService
 */
class ArgumentService
{

    /**
     * @var int
     */
    static protected $templateIdentifier = 0;

    /**
     * @var array
     */
    static protected $settings = [];
    public function __construct(private \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool)
    {
    }

    /**
     * @param int $identifier
     * @return array
     */
    public function getValues(int $identifier = 0): array
    {
        $values = [];

        $identifier = $this->sanitizeIdentifier($identifier);

        if ($identifier > 0) {

            $record = $this->getRecord($identifier);
            if (!empty($record)) {
                self::$settings = $this->getFlexFormService()->extractSettings($record['pi_flexform']);
                self::$templateIdentifier = (int)self::$settings['template'];

                foreach ($this->getTemplateService()->getFields() as $templateField) {
                    $value = $GLOBALS['TYPO3_REQUEST']->getParsedBody()[$templateField] ?? $GLOBALS['TYPO3_REQUEST']->getQueryParams()[$templateField] ?? null;
                    if ($value !== null) {
                        $values[$templateField] = $value;
                    }
                }
            }
        }
        return $values;
    }

    /**
     * @param int $identifier
     * @return int
     */
    protected function sanitizeIdentifier(int $identifier): int
    {
        if ($identifier < 1) {
            $arguments = $GLOBALS['TYPO3_REQUEST']->getParsedBody()['tx_formule_pi1'] ?? $GLOBALS['TYPO3_REQUEST']->getQueryParams()['tx_formule_pi1'] ?? null;
            if (!empty($arguments['values'])) {
                $identifier = (int)$arguments['values'];
            }
        }
        return $identifier;
    }

    /**
     * @param int $identifier
     * @return array|null
     */
    protected function getRecord($identifier)
    {
        $tableName = 'tt_content';

        /** @var QueryBuilder $query */
        $query = $this->getQueryBuilder($tableName);

        $query->select('uid', 'pi_flexform')
            ->from($tableName)
            ->where(
                $query->expr()->eq(
                    'uid',
                    (int)$identifier
                )
            );

        $record = $query
            ->executeQuery()
            ->fetchAssociative();

        return $record;
    }

    /**
     * @param string $tableName
     * @return object|QueryBuilder
     */
    protected function getQueryBuilder($tableName): QueryBuilder
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = $this->connectionPool;
        return $connectionPool->getQueryBuilderForTable($tableName);
    }

    /**
     * @return FlexFormService|object
     */
    protected function getFlexFormService()
    {
        return GeneralUtility::makeInstance(FlexFormService::class);
    }

    /**
     * @return TemplateService|object
     */
    protected function getTemplateService()
    {
        return GeneralUtility::makeInstance(TemplateService::class);
    }

    /**
     * Returns an instance of the page repository.
     *
     * @return \TYPO3\CMS\Core\Domain\Repository\PageRepository
     */
    protected function getPageRepository()
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Core\Domain\Repository\PageRepository::class);
    }

    /**
     * @return int
     */
    public static function getTemplateIdentifier(): int
    {
        return self::$templateIdentifier;
    }

    /**
     * @param int $templateIdentifier
     * @return void
     */
    public static function setTemplateIdentifier(int $templateIdentifier): void
    {
        self::$templateIdentifier = $templateIdentifier;
    }

    /**
     * @return array
     */
    public static function getSettings(): array
    {
        return self::$settings;
    }

}
