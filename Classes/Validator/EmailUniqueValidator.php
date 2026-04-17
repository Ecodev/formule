<?php
namespace Fab\Formule\Validator;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class EmailUniqueValidator
 */
class EmailUniqueValidator extends AbstractValidator
{

    public function __construct(private \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool)
    {
    }
    /**
     * @param array $values
     * @return array
     */
    public function validate(array $values): array
    {
        $messages = [];

        $tableName = $this->getTemplateService()->getPersistingTable();

        /** @var QueryBuilder $query */
        $query = $this->getQueryBuilder($tableName);

        /** @var DeletedRestriction $restriction */
        $restriction = GeneralUtility::makeInstance(DeletedRestriction::class);

        // We only want the "deleted" constraint
        $query
            ->getRestrictions()
            ->removeAll()
            ->add($restriction);

        $query
            ->select('*')
            ->from($tableName);

        $constraints[] = $query->expr()->eq(
            'email',
            $query->expr()->literal($values['email'])
        );

        // true means we are updating the record.
        $identifierField = $this->getTemplateService()->getIdentifierField();
        $identifierValue = $GLOBALS['TYPO3_REQUEST']->getParsedBody()[$identifierField] ?? $GLOBALS['TYPO3_REQUEST']->getQueryParams()[$identifierField] ?? null;

        if (!empty($identifierValue)) {
            $constraints[] = $query->expr()->eq(
                $identifierField,
                $query->expr()->literal($identifierValue)
            );
        }

        foreach ($constraints as $constraint) {
            $query->andWhere($constraint);
        }

        $record = $query
            ->executeQuery()
            ->fetchAssociative();

        if (!empty($record)) {
            $value = LocalizationUtility::translate('error.email.unique', 'formule');
            $messages['email'] = $value;
        }

        return $messages;
    }

    /**
     * @param string $tableName
     * @return object|QueryBuilder
     */
    protected function getQueryBuilder(string $tableName): QueryBuilder
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = $this->connectionPool;
        return $connectionPool->getQueryBuilderForTable($tableName);
    }

}
