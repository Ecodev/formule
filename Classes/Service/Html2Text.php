<?php
namespace Fab\Formule\Service;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Html2Text\LynxStrategy;
use Fab\Formule\Html2Text\RegexpStrategy;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @see http://www.chuggnutt.com/html2text
 */
class Html2Text implements SingletonInterface
{

    /**
     * @var \Fab\Formule\Html2Text\StrategyInterface
     */
    protected $converter;

    /**
     * @var array
     */
    protected $possibleConverters;

    /**
     * Returns a class instance
     *
     * @return \Fab\Formule\Service\Html2Text
     */
    static public function getInstance()
    {
        return GeneralUtility::makeInstance(self::class);
    }

    /**
     * Constructor
     *
     * @return \Fab\Formule\Service\Html2Text
     */
    public function __construct()
    {
        $this->possibleConverters[] = GeneralUtility::makeInstance(LynxStrategy::class);
        $this->possibleConverters[] = GeneralUtility::makeInstance(RegexpStrategy::class);
    }

    /**
     * Convert HTML using the best strategy
     *
     * @param string $content to be converted
     * @return string
     */
    public function convert($content)
    {
        if (empty($this->converter)) {
            $this->converter = $this->findBestConverter();
        }
        return $this->converter->convert($content);
    }

    /**
     * Find the best suitable converter
     *
     * @return \Fab\Formule\Html2Text\StrategyInterface
     */
    public function findBestConverter()
    {

        if (!empty($this->converter)) {
            return $this->converter;
        }

        // Else find the best suitable converter
        $converter = end($this->possibleConverters);
        foreach ($this->possibleConverters as $possibleConverter) {
            /** @var \Fab\Formule\Html2Text\StrategyInterface $possibleConverter */
            if ($possibleConverter->available()) {
                $converter = $possibleConverter;
                break;
            }
        }

        return $converter;
    }

    /**
     * Set strategy
     *
     * @param \Fab\Formule\Html2Text\StrategyInterface $converter
     * @return void
     */
    public function setConverter(\Fab\Formule\Html2Text\StrategyInterface $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @return \Fab\Formule\Html2Text\StrategyInterface
     */
    public function getConverter()
    {
        return $this->converter;
    }

    /**
     * @return Array
     */
    public function getPossibleConverters()
    {
        return $this->possibleConverters;
    }

    /**
     * @param array $possibleConverters
     */
    public function setPossibleConverters(array $possibleConverters)
    {
        $this->possibleConverters = $possibleConverters;
    }

    /**
     * @param \Fab\Formule\Html2Text\StrategyInterface $possibleConverter
     */
    public function addPossibleConverter($possibleConverter)
    {
        $this->possibleConverters[] = $possibleConverter;
    }

}
