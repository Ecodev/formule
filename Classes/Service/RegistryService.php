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

use TYPO3\CMS\Core\Registry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Registry service wrapper.
 */
class RegistryService
{

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->getRegistry()->set($this->getKey(), $key, $value);
        return $this;
    }

    /**
     * Fetch the entry of the registry Entry and clean up the registry afterwards.
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        $entry = $this->getRegistry()->get($this->getKey(), $key);
        $this->getRegistry()->remove($this->getKey(), $key);
        return $entry;
    }

    /**
     * Returns an instance of the Frontend object.
     *
     * @return Registry
     */
    protected function getRegistry()
    {
        return GeneralUtility::makeInstance(Registry::class);
    }

    /**
     * @return string
     */
    protected function getKey()
    {
        $identifier = $this->getFrontendUser()->getKey('ses', 'formule_identifier');
        if (is_null($identifier)) {
            $identifier = $this->getFrontendUser()->id;
            $this->getFrontendUser()->setKey('ses', 'formule_identifier', $identifier);
        }
        return 'Fab\Formule\\' . $identifier;
    }

    /**
     * Returns an instance of the current Frontend User.
     *
     * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected function getFrontendUser()
    {
        return $GLOBALS['TSFE']->fe_user;
    }

}
