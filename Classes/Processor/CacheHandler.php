<?php
namespace Fab\Formule\Processor;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\CacheService;

/**
 * Class clear the cache for certain pages.
 * Only an example class to be copy / pasted and adjusted!!!
 */
class CacheHandler extends AbstractProcessor
{

    /**
     * @param array $values
     * @param string $insertOrUpdate
     * @return array
     */
    public function process(array $values, $insertOrUpdate = '')
    {
        $pageIdentifiers = [1, 2, 3]; # to be defined what page id should have a cache clear.
        $this->getCacheService()->clearPageCache($pageIdentifiers);

        return $values;
    }

    /**
     * @return CacheService
     */
    protected function getCacheService()
    {
        return $this->getObjectManager()->get(CacheService::class);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
    }

}
