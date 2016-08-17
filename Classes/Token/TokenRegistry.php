<?php
namespace Fab\Formule\Token;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Class TokenRegistry
 */
class TokenRegistry implements SingletonInterface
{

    /**
     * @var array
     */
    protected $registry = [];

    /**
     * @var array
     */
    protected $extensions = [];

    /**
     * @var string
     */
    protected $template = '';

    /**
     * Returns a class instance
     *
     * @return TokenRegistry
     */
    static public function getInstance()
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

    /**
     * Creates this object.
     */
    public function __construct()
    {
        // Corresponds to "token"
        $this->template = '


CREATE TABLE %s (
 	%s varchar(36)  DEFAULT \'\' NOT NULL,
);


';
    }

    /**
     * Adds a new token configuration to this registry.
     * TCA changes are directly applied
     *
     * @param string $extensionKey Extension key to be used
     * @param string $tableName Name of the table to be registered
     * @param string $tokenField Name of the field to be registered
     * @param array $options Additional configuration options
     *              + fieldList: field configuration to be added to showitems
     *              + typesList: list of types that shall visualize the token field
     *              + position: insert position of the token field
     *              + label: backend label of the token field
     *              + fieldConfiguration: TCA field config array to override defaults
     * @return bool
     */
    public function add($extensionKey, $tableName, $tokenField = 'token', array $options = [])
    {
        $didRegister = FALSE;
        if (empty($tableName) || !is_string($tableName)) {
            throw new \InvalidArgumentException('No or invalid table name "' . $tableName . '" given.', 1369122038);
        }

        if (!$this->isRegistered($tableName, $tokenField)) {
            $this->registry[$tableName][$tokenField] = $options;
            $this->extensions[$extensionKey][$tableName]['tokenField'][$tokenField] = $tokenField;

            if (!isset($GLOBALS['TCA'][$tableName]['columns']) && isset($GLOBALS['TCA'][$tableName]['ctrl']['dynamicConfigFile'])) {
                // Handle deprecated old style dynamic TCA column loading.
                ExtensionManagementUtility::loadNewTcaColumnsConfigFiles();
            }

            if (isset($GLOBALS['TCA'][$tableName]['columns'])) {
                $this->applyTcaForTableAndField($tableName, $tokenField);
                $didRegister = TRUE;
            }
        }

        return $didRegister;
    }

    /**
     * @return array
     */
    public function getExtensionKeys()
    {
        return array_keys($this->extensions);
    }

    /**
     * @param string $tableName Name of the table to be looked up
     * @param string $fieldName Name of the field to be looked up
     * @return boolean
     */
    public function isRegistered($tableName, $fieldName = 'token')
    {
        return isset($this->registry[$tableName][$fieldName]);
    }

    /**
     * @param string $tableName Name of the table to be looked up
     * @return string
     */
    public function getTokenField($tableName)
    {
        return key($this->registry[$tableName]);
    }

    /**
     * Generates tables definitions for all registered tables.
     *
     * @return string
     */
    protected function getDatabaseTableDefinitions()
    {
        $sql = '';
        foreach ($this->getExtensionKeys() as $extensionKey) {
            $sql .= $this->getDatabaseTableDefinition($extensionKey);
        }
        return $sql;
    }

    /**
     * Generates table definitions for registered tables by an extension.
     *
     * @param string $extensionKey Extension key to have the database definitions created for
     * @return string
     */
    protected function getDatabaseTableDefinition($extensionKey)
    {
        if (!isset($this->extensions[$extensionKey]) || !is_array($this->extensions[$extensionKey])) {
            return '';
        }
        $sql = '';

        foreach ($this->extensions[$extensionKey] as $tableName => $fields) {
            foreach ($fields['tokenField'] as $fieldName) {
                $sql .= sprintf($this->template, $tableName, $fieldName);
            }
        }

        return $sql;
    }

    /**
     * Applies the additions directly to the TCA
     *
     * @param string $tableName
     * @param string $tokenField
     * @param string $rankFieldName
     */
    protected function applyTcaForTableAndField($tableName, $tokenField)
    {
        $this->addTokenField($tableName, $tokenField, $this->registry[$tableName][$tokenField]);
        $this->addToAllTCAtypes($tableName, $tokenField, $this->registry[$tableName][$tokenField]);
    }

    /**
     * Add a new field into the TCA types -> showitem
     *
     * @param string $tableName
     * @param string $fieldName
     * @param array $options Additional configuration options
     *              + fieldList: field configuration to be added to showitems
     *              + typesList: list of types that shall visualize the token field
     *              + position: insert position of the token field
     * @return void
     */
    protected function addToAllTCAtypes($tableName, $fieldName, array $options)
    {

        // Makes sure to add more TCA to an existing structure
        if (isset($GLOBALS['TCA'][$tableName]['columns'])) {

            $typesList = '';
            if (!empty($options['typesList'])) {
                $typesList = $options['typesList'];
            }

            $position = '';
            if (!empty($options['position'])) {
                $position = $options['position'];
            }

            // Makes the new "token" field to be visible in TSFE.
            ExtensionManagementUtility::addToAllTCAtypes($tableName, $fieldName, $typesList, $position);
        }
    }

    /**
     * Add a new TCA Column
     *
     * @param string $tableName
     * @param string $fieldName
     * @param array $options Additional configuration options
     *              + fieldConfiguration: TCA field config array to override defaults
     *              + label: backend label of the token field
     *              + interface: boolean if the token should be included in the "interface" section of the TCA table
     *              + l10n_mode
     *              + l10n_display
     * @return void
     */
    protected function addTokenField($tableName, $fieldName, array $options)
    {

        // Makes sure to add more TCA to an existing structure
        if (isset($GLOBALS['TCA'][$tableName]['columns'])) {
            // Take specific label into account
            $label = 'LLL:EXT:formule/Resources/Private/Language/locallang.xlf:token';
            if (!empty($options['label'])) {
                $label = $options['label'];
            }

            // Take specific value of exclude flag into account
            $exclude = true;
            if (isset($options['exclude'])) {
                $exclude = (bool)$options['exclude'];
            }

            $fieldConfiguration = empty($options['fieldConfiguration']) ? [] : $options['fieldConfiguration'];

            $columns = [
                $fieldName => [
                    'exclude' => $exclude,
                    'label' => $label,
                    'config' => static::getTokenFieldConfiguration($fieldConfiguration),
                ],
            ];

            // Add field to interface list per default (unless the 'interface' property is FALSE)
            if (
                (!isset($options['interface']) || $options['interface'])
                && !empty($GLOBALS['TCA'][$tableName]['interface']['showRecordFieldList'])
                && !GeneralUtility::inList($GLOBALS['TCA'][$tableName]['interface']['showRecordFieldList'], $fieldName)
            ) {
                $GLOBALS['TCA'][$tableName]['interface']['showRecordFieldList'] .= ',' . $fieldName;
            }

            // Adding fields to an existing table definition
            ExtensionManagementUtility::addTCAcolumns($tableName, $columns);
        }
    }

    /**
     * @param array $fieldConfigurationOverride Changes to the default configuration
     * @return array
     * @api
     */
    static public function getTokenFieldConfiguration(array $fieldConfigurationOverride = [])
    {
        // Forges a new field, default name is "token"
        $fieldConfiguration = [
            'type' => 'input',
            'eval' => 'trim',
        ];

        // Merge changes to TCA configuration
        if (!empty($fieldConfigurationOverride)) {
            ArrayUtility::mergeRecursiveWithOverrule(
                $fieldConfiguration,
                $fieldConfigurationOverride
            );
        }

        return $fieldConfiguration;
    }

    /**
     * A slot method to inject the required token field
     * tables definition string
     *
     * @param array $sqlString
     * @return array
     */
    public function addTokenDatabaseSchemaToTablesDefinition(array $sqlString)
    {
        $sqlString[] = $this->getDatabaseTableDefinitions();
        return ['sqlString' => $sqlString];
    }

    /**
     * A slot method to inject the required token field
     * extension to the tables definition string
     *
     * @param array $sqlString
     * @param string $extensionKey
     * @return array
     */
    public function addExtensionTokenDatabaseSchemaToTablesDefinition(array $sqlString, $extensionKey)
    {
        $sqlString[] = $this->getDatabaseTableDefinition($extensionKey);
        return ['sqlString' => $sqlString, 'extensionKey' => $extensionKey];
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

}
