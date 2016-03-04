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
     * @param bool $fetchAndFlush
     * @return mixed
     */
    public function get($key, $fetchAndFlush = true)
    {
        $entry = $this->getRegistry()->get($this->getKey(), $key);

        if ($fetchAndFlush) {
            $this->getRegistry()->remove($this->getKey(), $key);
        }
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
        if ('' === session_id()) {
            session_start();
        }
        return 'Fab\Formule\\' . session_id();
    }

}
