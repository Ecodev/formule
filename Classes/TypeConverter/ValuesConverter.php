<?php
namespace Fab\Formule\TypeConverter;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Service\ArgumentService;
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
        return $this->getArgumentService()->getValues($source);
    }

    /**
     * @return ArgumentService
     */
    protected function getArgumentService()
    {
        return GeneralUtility::makeInstance(ArgumentService::class);
    }

}