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
 * TemplateAnalyserService
 */
class TemplateAnalyserService
{

    /**
     * @var string
     */
    protected $templateCode;

    /**
     * Constructor.
     * @param string $template
     */
    public function __construct($template)
    {
        $templateNameAndPath = GeneralUtility::getFileAbsFileName($template);
        $templateCode = file_get_contents($templateNameAndPath);

        // Strip content after "section".
        $this->templateCode = preg_replace('/.*section name="main">/isU', '', $templateCode);
        $this->templateCodeRow = $templateCode;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        $templateFields = [];
        preg_match_all('/name="(\w+)"/', $this->templateCode, $matches);

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
        preg_match_all('/name="(\w+)".*required/isU', $this->templateCode, $matches);

        $requiredFields = [];

        if (!empty($matches[1])) {
            $requiredFields = $matches[1];
        }
        return $requiredFields;
    }

}