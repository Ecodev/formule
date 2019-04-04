<?php
namespace Fab\Formule\Service;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

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
                    $value = GeneralUtility::_GP($templateField);
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
            $arguments = GeneralUtility::_GP('tx_formule_pi1');
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
        $clause = 'uid = ' . $identifier;
        $clause .= $this->getPageRepository()->enableFields($tableName);
        $clause .= $this->getPageRepository()->deleteClause($tableName);
        $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('uid, pi_flexform', $tableName, $clause);
        return $record;
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
     * @return \TYPO3\CMS\Frontend\Page\PageRepository
     */
    protected function getPageRepository()
    {
        return $GLOBALS['TSFE']->sys_page;
    }

    /**
     * @return int
     */
    public static function getTemplateIdentifier(): int
    {
        return self::$templateIdentifier;
    }

    /**
     * @return array
     */
    public static function getSettings(): array
    {
        return self::$settings;
    }

}
