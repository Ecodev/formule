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
use SimpleXMLElement;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TemplateService
 */
class TemplateService implements SingletonInterface
{
    /**
     * @var int
     */
    protected $templateIdentifier;

    /**
     * @var string
     */
    protected $mainSection;

    /**
     * @var string
     */
    protected $feedbackSection;

    /**
     * Constructor.
     *
     * @param string $templateIdentifier
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
     * @return string
     */
    public function getPersistingTable()
    {
        return (string)$this->getPersistingTableName();
    }

    /**
     * @return mixed
     */
    public function get($key)
    {
        $ts = $this->getTypoScriptService()->getSettings();
        return empty($ts['templates'][$this->templateIdentifier][$key]) ? null : $ts['templates'][$this->templateIdentifier][$key];
    }

    /**
     * @return array
     */
    public function getFields()
    {
        $templateFields = [];
        preg_match_all('/name="(\w+)"/', $this->getMainSection(), $matches);

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
        preg_match_all('/name="(\w+)".*required/isU', $this->getMainSection(), $matches);

        $requiredFields = [];

        if (!empty($matches[1])) {
            $requiredFields = $matches[1];
        }
        return $requiredFields;
    }

    /**
     * @return string
     */
    public function getMainSection()
    {
        if (is_null($this->mainSection)) {
            $template = $this->getPath();
            $templateNameAndPath = GeneralUtility::getFileAbsFileName($template);
            $section = file_get_contents($templateNameAndPath);

            // Extract section "main".
            $section = preg_replace('/.*section name="main">(.*)/isU', '$1', $section);
            $limit = strpos($section, '</f:section');
            $this->mainSection = substr($section, 0, $limit);
        }
        return $this->mainSection;
    }

    /**
     * @return string
     */
    public function getFeedbackSection()
    {
        if (is_null($this->feedbackSection)) {
            $template = $this->getPath();
            $templateNameAndPath = GeneralUtility::getFileAbsFileName($template);
            $templateCode = file_get_contents($templateNameAndPath);

            // Strip content after "section".
            $feedbackSection = preg_replace('/.*section name="feedback".*>(.+)/isU', '$1', $templateCode);
            $limit = strpos($feedbackSection, '</f:section');
            $this->feedbackSection = substr($feedbackSection, 0, $limit);
        }
        return $this->feedbackSection;
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