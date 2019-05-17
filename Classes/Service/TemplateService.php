<?php
namespace Fab\Formule\Service;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use RuntimeException;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * TemplateService
 */
class TemplateService
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
     * @var array
     */
    protected $settings;

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
    public function __construct($templateIdentifier = 0)
    {
        if ((int)$templateIdentifier > 0) {
            ArgumentService::setTemplateIdentifier($templateIdentifier);
        }

        $this->templateIdentifier = (int)$templateIdentifier > 0
            ? (int)$templateIdentifier
            : ArgumentService::getTemplateIdentifier();
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->get('path');
    }

    /**
     * @return array
     */
    public function getAssets(): array
    {
        $assets = $this->get('asset');
        return is_array($assets) ? $assets : [];
    }

    /**
     * @return string
     */
    public function getResolvedPath(): string
    {
        return GeneralUtility::getFileAbsFileName($this->getPath());
    }

    /**
     * @return string
     */
    public function getPersistingTableName(): string
    {
        $persist = $this->get('persist');
        $tableName = is_array($persist) && empty($persist['tableName']) ? '' : $persist['tableName'];
        return $tableName;
    }

    /**
     * @return string
     */
    public function getIdentifierField(): string
    {
        $persist = $this->get('persist');
        $identifierField = is_array($persist) && empty($persist['identifierField']) ? 'uid' : $persist['identifierField'];
        return $identifierField;
    }

    /**
     * @return bool
     */
    public function hasPersistingTable(): bool
    {
        return $this->getPersistingTable() !== '';
    }

    /**
     * @return bool
     */
    public function hasRedirect(): bool
    {
        $redirect = $this->get('redirect');
        return is_array($redirect) && !empty($redirect);
    }

    /**
     * @return bool
     */
    public function isDefaultRedirectAction(): bool
    {
        return $this->getRedirectAction() === 'feedback';
    }

    /**
     * @param array $values
     * @return string
     */
    public function getRedirectUrl(array $values): string
    {
        $arguments = [];

        if ($this->hasIdentifierValue()) {
            $arguments[$this->getIdentifierField()] = $this->getIdentifierValue();
        } elseif (!empty($values['uid'])) {
            $arguments[$this->getIdentifierField()] = $values['uid'];
        }

        $uriBuilder = $this->getUriBuilder()
            ->reset()
            ->setTargetPageUid($this->getRedirectPageUid())
            ->setUseCacheHash(false)
            ->setCreateAbsoluteUri(true)
            ->setArguments($arguments);

        return $uriBuilder->build();
    }

    /**
     * @return int|null
     */
    public function getRedirectPageUid(): ?int
    {

        $redirect = $this->get('redirect');
        return is_array($redirect) && !empty($redirect['pageUid']) ? $redirect['pageUid'] : null;
    }

    /**
     * @return string
     */
    public function getRedirectAction(): string
    {

        $redirect = $this->get('redirect');
        return is_array($redirect) && !empty($redirect['action']) ? $redirect['action'] : 'feedback';
    }

    /**
     * @return string
     */
    public function getRedirectController(): string
    {
        $redirect = $this->get('redirect');
        return is_array($redirect) && !empty($redirect['controller']) ? $redirect['controller'] : 'Form';
    }

    /**
     * @return bool
     */
    public function hasWarnings(): bool
    {
        $warnings = $this->getWarnings();
        return !empty($warnings);
    }

    /**
     * @return array
     */
    public function getDefaultValues(): array
    {
        $persist = $this->get('persist');

        $defaultValues = is_array($persist) && empty($persist['defaultValues']) ? [] : $persist['defaultValues'];

        return $defaultValues;
    }

    /**
     * @return array
     */
    public function getMappings(): array
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
    public function getWarnings(): array
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
    public function getProcessors(): array
    {
        $persist = $this->get('persist');
        $processors = is_array($persist) && empty($persist['processors']) ? [] : $persist['processors'];
        return $processors;
    }

    /**
     * @return array
     */
    public function getLoaders(): array
    {
        $loaders = $this->get('loaders');
        return is_array($loaders) ? $loaders : [];
    }

    /**
     * @return array
     */
    public function getFinishers(): array
    {
        $finishers = $this->get('finishers');
        return is_array($finishers) ? $finishers : [];
    }

    /**
     * @return array
     */
    public function getValidators(): array
    {
        $validators = $this->get('validators');
        return is_array($validators) ? $validators : [];
    }

    /**
     * @return string
     */
    public function getPersistingTable(): string
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
    public function hasHoneyPot(): bool
    {
        $sectionCode = $this->getSection(self::SECTION_MAIN);
        return (bool)preg_match('/<.*:honeyPot/', $sectionCode);
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        $templateFields = [];
        preg_match_all('/name="(\w+)"/', $this->getSection(self::SECTION_MAIN), $matches);

        if (!empty($matches[1])) {
            $templateFields = $matches[1];
        }

        $templateFields = array_unique($templateFields);

        $ts = $this->getTypoScriptService()->getSettings();
        $excludedFields = GeneralUtility::trimExplode(',', $ts['excludedFieldsFromTemplateParsing'], true);
        foreach ($excludedFields as $excludedField) {
            $key = array_search($excludedField, $templateFields);
            if (false !== $key) {
                unset($templateFields[$key]);
            }
        }

        return $templateFields;
    }

    /**
     * @return string
     */
    public function getPreferredEmailBodyEncoding(): string
    {
        $preferEmailBodyEncoding = $this->get('preferEmailBodyEncoding');
        if (is_null($preferEmailBodyEncoding)) {

            $ts = $this->getTypoScriptService()->getSettings();
            $preferEmailBodyEncoding = $ts['preferEmailBodyEncoding'];
        }

        return $preferEmailBodyEncoding;
    }

    /**
     * @return array
     */
    public function getRequiredFields(): array
    {
        $requiredFields = [];
        foreach ($this->getFields() as $field) {
            $expression = sprintf('/name="%s"[^>]*required[^>]*>/s', $field);
            if (preg_match($expression, $this->getSection(self::SECTION_MAIN))) {
                $requiredFields[] = $field;
            }
        }
        return $requiredFields;
    }

    /**
     * @param string $sectionName
     * @return string
     */
    public function getSection($sectionName): string
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
     * @return mixed|null
     */
    public function getVariable($name)
    {
        if (strpos($name, 'variable.') !== false) {
            $name = str_replace('variable.', '', $name); // strip first segment.
        }
        $variable = $this->get('variable');
        return is_array($variable) && isset($variable[$name]) ? $variable[$name] : null;
    }

    /**
     * @return int
     */
    public function getTemplateIdentifier(): int
    {
        return $this->templateIdentifier;
    }

    /**
     * @param string $templateCode
     * @return array
     * @throws \TYPO3\CMS\Fluid\Core\Parser\Exception
     */
    protected function getNamespaceDefinitions($templateCode): array
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
    protected function extractNamespaceDefinitions($templateString): string
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
     * @return string
     */
    protected function hasIdentifierValue(): string
    {
        return (bool)$this->getIdentifierValue();
    }

    /**
     * @return string
     */
    protected function getIdentifierValue(): string
    {
        $identifierField = $this->getIdentifierField();
        return (string)GeneralUtility::_GP($identifierField);
    }

    /**
     * @return UriBuilder
     */
    protected function getUriBuilder(): UriBuilder
    {
        /** @var $uriBuilder UriBuilder */
        $uriBuilder = $this->getObjectManager()->get(UriBuilder::class);
        return $uriBuilder;
    }

    /**
     * @return TypoScriptService
     */
    protected function getTypoScriptService(): TypoScriptService
    {
        return GeneralUtility::makeInstance(TypoScriptService::class);
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService(): \TYPO3\CMS\Lang\LanguageService
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected function getObjectManager(): \TYPO3\CMS\Extbase\Object\ObjectManager
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
    }

}
