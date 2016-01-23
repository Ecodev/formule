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

use DOMDocument;
use DOMXPath;
use RuntimeException;
use SimpleXMLElement;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TemplateService
 */
class TemplateService implements SingletonInterface
{
    const SECTION_MAIN = 'main';
    const SECTION_FEEDBACK = 'feedback';
    const SECTION_EMAIL_ADMIN = 'emailAdmin';
    const SECTION_EMAIL_USER = 'emailUser';
    /**
     * @var int
     */
    protected $templateIdentifier;

    /**
     * @var array
     */
    protected $sections;

    /**
     * @var array
     */
    protected $namespaces;

    /**
     * @var string
     */
    static public $SCAN_PATTERN_NAMESPACEDECLARATION = '/(?<!\\\\){namespace\\s*(?P<identifier>[a-zA-Z]+[a-zA-Z0-9]*)\\s*=\\s*(?P<phpNamespace>(?:[A-Za-z0-9\.]+|Tx)(?:LEGACY_NAMESPACE_SEPARATOR\\w+|FLUID_NAMESPACE_SEPARATOR\\w+)+)\\s*}/m';

    /**
     * @var string
     */
    static public $SCAN_PATTERN_XMLNSDECLARATION = '/\sxmlns:(?P<identifier>.*?)="(?P<xmlNamespace>.*?)"/m';

    /**
     * This pattern detects the default xml namespace
     *
     */
    static public $SCAN_PATTERN_DEFAULT_XML_NAMESPACE = '/^http\:\/\/typo3\.org\/ns\/(?P<PhpNamespace>.+)$/s';

    /**
     * Constructor.
     *
     * @param int $templateIdentifier
     */
    public function __construct($templateIdentifier)
    {
        $this->templateIdentifier = (int)$templateIdentifier;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->get('path');
    }

    /**
     * @return array
     */
    public function getAssets()
    {
        $assets = $this->get('asset');
        return is_array($assets) ? $assets : [];
    }

    /**
     * @return string
     */
    public function getResolvedPath()
    {
        return GeneralUtility::getFileAbsFileName($this->getPath());
    }

    /**
     * @return string
     */
    public function getPersistingTableName()
    {
        $persist = $this->get('persist');
        $tableName = is_array($persist) && empty($persist['tableName']) ? '' : $persist['tableName'];
        return $tableName;
    }

    /**
     * @return bool
     */
    public function hasPersistingTable()
    {
        return $this->getPersistingTable() !== '';
    }

    /**
     * @return bool
     */
    public function hasWarnings()
    {
        $warnings = $this->getWarnings();
        return !empty($warnings);
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        $persist = $this->get('persist');

        $defaultValues = is_array($persist) && empty($persist['defaultValues']) ? [] : $persist['defaultValues'];

        return $defaultValues;
    }

    /**
     * @return array
     */
    public function getMappings()
    {
        $persist = $this->get('persist');

        $mappings = is_array($persist) && empty($persist['mappings']) ? [] : $persist['mappings'];

        $ts = $this->getTypoScriptService()->getSettings();
        $tableName = $this->getPersistingTableName();

        if (isset($ts['defaultMappings'][$tableName])) {
            $mappings = array_merge($ts['defaultMappings'][$tableName], $mappings);
        }

        return $mappings;
    }

    /**
     * @return array
     */
    public function getWarnings()
    {
        $warnings = [];
        if ($this->hasPersistingTable()) {

            if (!isset($GLOBALS['TCA'][$this->getPersistingTableName()])) {
                $warnings[] =
                    '- ' .
                    $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:warning.missing.tca')
                    . ' "' . $this->getPersistingTableName() . '"';
            }

            foreach ($this->getFields() as $field) {
                if (!isset($GLOBALS['TCA'][$this->getPersistingTableName()]['columns'][$field])) {
                    $warnings[] =
                        '- ' .
                        $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:warning.missing.field')
                        . ' "' . $field . '". '
                        . $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:warning.mapping.necessary');
                }
            }
        }
        return $warnings;
    }

    /**
     * @return array
     */
    public function getProcessors()
    {
        $persist = $this->get('persist');
        $processors = is_array($persist) && empty($persist['processors']) ? [] : $persist['processors'];
        return $processors;
    }

    /**
     * @return array
     */
    public function getLoaders()
    {
        $loaders = $this->get('loaders');
        return is_array($loaders) ? $loaders : [];
    }

    /**
     * @return array
     */
    public function getValidators()
    {
        $validators = $this->get('validators');
        return is_array($validators) ? $validators : [];
    }

    /**
     * @return string
     */
    public function getPersistingTable()
    {
        return (string)$this->getPersistingTableName();
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        $ts = $this->getTypoScriptService()->getSettings();

        if (empty($ts['templates'][$this->templateIdentifier])) {
            $message = 'Formule: I could not find a template for the give key "' . $this->templateIdentifier . '"';
            throw new RuntimeException($message, 1453274963);
        }
        return empty($ts['templates'][$this->templateIdentifier][$key]) ? null : $ts['templates'][$this->templateIdentifier][$key];
    }

    /**
     * @return bool
     */
    public function hasHoneyPot()
    {
        $sectionCode = $this->getSection(self::SECTION_MAIN);
        return (bool)preg_match('/<.*:honeyPot/', $sectionCode);
    }

    /**
     * @return array
     */
    public function getFields()
    {
        $templateFields = [];
        preg_match_all('/name="(\w+)"/', $this->getSection(self::SECTION_MAIN), $matches);

        if (!empty($matches[1])) {
            $templateFields = $matches[1];
        }

        $key = array_search('values', $templateFields);
        unset($templateFields[$key]);

        return $templateFields;
    }

    /**
     * @return array
     */
    public function getRequiredFields()
    {
        preg_match_all('/name="(\w+)".*required=/isU', $this->getSection(self::SECTION_MAIN), $matches);

        $requiredFields = [];

        if (!empty($matches[1])) {
            $requiredFields = $matches[1];
        }
        return $requiredFields;
    }

    /**
     * @param string $sectionName
     * @return string
     */
    public function getSection($sectionName)
    {
        if (!isset($this->sections[$sectionName])) {
            $template = $this->getPath();
            $templateNameAndPath = GeneralUtility::getFileAbsFileName($template);
            $templateCode = file_get_contents($templateNameAndPath);

            $sectionCode = '';

            // Extract section.
            if (preg_match('/.*section name="' . $sectionName . '">(.*)/isU', $templateCode)) {
                $sectionCode = preg_replace('/.*section name="' . $sectionName . '">(.*)/isU', '$1', $templateCode);
                $limit = strpos($sectionCode, '</f:section');
                $sectionCode = substr($sectionCode, 0, $limit);

                // Append namespace but not for the main section
                if ($sectionName !== 'main') {
                    $sectionCode .= implode("\n", $this->getNamespaceDefinitions($templateCode));
                }
            }
            $this->sections[$sectionName] = $sectionCode;
        }
        return $this->sections[$sectionName];
    }

    /**
     * @param string $templateCode
     * @return array
     * @throws \TYPO3\CMS\Fluid\Core\Parser\Exception
     */
    protected function getNamespaceDefinitions($templateCode)
    {
        // Only analyse once
        if (is_null($this->namespaces)) {
            $this->namespaces = [];
            $this->extractNamespaceDefinitions($templateCode);
        }

        $namespaces = [];
        foreach ($this->namespaces as $prefix => $namespace) {
            $namespaces[] = sprintf('{namespace %s=%s}', $prefix, $namespace);
        }
        return $namespaces;
    }

    /**
     * Extracts namespace definitions out of the given template string and sets
     * $this->namespaces.
     *
     * @param string $templateString Template string to extract the namespaces from
     * @return string The updated template string without namespace declarations inside
     * @throws \TYPO3\CMS\Fluid\Core\Parser\Exception if a namespace can't be resolved or has been declared already
     */
    protected function extractNamespaceDefinitions($templateString)
    {
        $matches = array();
        preg_match_all(self::$SCAN_PATTERN_XMLNSDECLARATION, $templateString, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            // skip reserved "f" namespace identifier
            if ($match['identifier'] === 'f') {
                continue;
            }
            if (array_key_exists($match['identifier'], $this->namespaces)) {
                throw new \TYPO3\CMS\Fluid\Core\Parser\Exception(sprintf('Namespace identifier "%s" is already registered. Do not re-declare namespaces!', $match['identifier']), 1331135889);
            }
            $matchedPhpNamespace = array();
            if (preg_match(self::$SCAN_PATTERN_DEFAULT_XML_NAMESPACE, $match['xmlNamespace'], $matchedPhpNamespace) === 0) {
                continue;
            }
            $phpNamespace = str_replace('/', '\\', $matchedPhpNamespace['PhpNamespace']);
            $this->namespaces[$match['identifier']] = $phpNamespace;
        }
        $matches = array();
        preg_match_all(self::$SCAN_PATTERN_NAMESPACEDECLARATION, $templateString, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if (array_key_exists($match['identifier'], $this->namespaces)) {
                throw new \TYPO3\CMS\Fluid\Core\Parser\Exception(sprintf('Namespace identifier "%s" is already registered. Do not re-declare namespaces!', $match['identifier']), 1224241246);
            }
            $this->namespaces[$match['identifier']] = $match['phpNamespace'];
        }
        if ($matches !== array()) {
            $templateString = preg_replace(self::$SCAN_PATTERN_NAMESPACEDECLARATION, '', $templateString);
        }

        return $templateString;
    }

    /**
     * @return TypoScriptService
     */
    protected function getTypoScriptService()
    {
        return GeneralUtility::makeInstance(TypoScriptService::class);
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

}