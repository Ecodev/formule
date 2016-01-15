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
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * TemplateService
 */
class TemplateService
{
    /**
     * @var int
     */
    protected $templateIdentifier;

    /**
     * @var string
     */
    protected $templateCode;

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
     * @return string
     */
    public function getResolvedPath()
    {
        return GeneralUtility::getFileAbsFileName($this->getPath());
    }

    /**
     * @return bool
     */
    public function hasPersistingTable()
    {
        return (bool)$this->get('persistToTable');
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
    public function getWarnings()
    {
        $warnings = [];
        if (!isset($GLOBALS['TCA'][$this->get('persistToTable')])) {
            $warnings[] =
                '- ' .
                $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:warning.missing.tca')
                . ' "' . $this->get('persistToTable') . '"';
        }

        foreach ($this->getFields() as $field) {
            if (!isset($GLOBALS['TCA'][$this->get('persistToTable')]['columns'][$field])) {
                $warnings[] =
                    '- ' .
                    $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:warning.missing.field')
                    . ' "' . $field . '". '
                    . $this->getLanguageService()->sL('LLL:EXT:formule/Resources/Private/Language/locallang.xlf:warning.mapping.necessary');
            }
        }
        return $warnings;
    }

    /**
     * @return string
     */
    public function getPersistingTable()
    {
        return (string)$this->get('persistToTable');
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
        preg_match_all('/name="(\w+)"/', $this->getTemplateCode(), $matches);

        if (!empty($matches[1])) {
            $templateFields = $matches[1];
        }
        return $templateFields;
    }

    /**
     * @return array
     */
    public function getRequiredFields()
    {
        preg_match_all('/name="(\w+)".*required/isU', $this->getTemplateCode(), $matches);

        $requiredFields = [];

        if (!empty($matches[1])) {
            $requiredFields = $matches[1];
        }
        return $requiredFields;
    }

    /**
     * @return string
     */
    protected function getTemplateCode()
    {
        if (is_null($this->templateCode)) {
            $template = $this->getPath();
            $templateNameAndPath = GeneralUtility::getFileAbsFileName($template);
            $templateCode = file_get_contents($templateNameAndPath);

            // Strip content after "section".
            $this->templateCode = preg_replace('/.*section name="main">/isU', '', $templateCode);
        }
        return $this->templateCode;
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