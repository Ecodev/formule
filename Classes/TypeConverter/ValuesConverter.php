<?php
namespace Fab\Formule\TypeConverter;

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

use Fab\Formule\Service\FlexFormService;
use Fab\Formule\Service\TemplateAnalyserService;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;

/**
 * Convert a weird array given as input by DataTables into a key value array.
 */
class ValuesConverter extends AbstractTypeConverter
{

    /**
     * @var array<string>
     */
    protected $sourceTypes = array('int');

    /**
     * @var string
     */
    protected $targetType = 'array';

    /**
     * @var int
     */
    protected $priority = 1;

    /**
     * Actually convert from $source to $targetType
     *
     * @param string $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface $configuration
     * @throws \Exception
     * @throws \TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException
     * @return File
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = array(), PropertyMappingConfigurationInterface $configuration = NULL)
    {

        $record = $this->getRecord((int)$source);
        $values = [];
        if (!empty($record)) {
            $settings = $this->getFlexFormService()->extractSettings($record['pi_flexform']);

            $templateAnalyser = $this->getTemplateAnalyserService($settings['template']);
            $templateFields = $templateAnalyser->getFields();

            foreach ($templateFields as $templateField) {
                $value = GeneralUtility::_GP($templateField);
                if (!empty($value)) {
                    $values[$templateField] = $value;
                }
            }
        }

        return $values;
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
     * @return FlexFormService
     */
    protected function getFlexFormService()
    {
        return GeneralUtility::makeInstance(FlexFormService::class);
    }

    /**
     * @return TemplateAnalyserService
     */
    protected function getTemplateAnalyserService($templateNameAndPath)
    {
        return GeneralUtility::makeInstance(TemplateAnalyserService::class, $templateNameAndPath);
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