<?php
namespace Fab\Formule\Loader;

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

use Fab\Formule\Service\RegistryService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RegistryLoader
 * Useful to transmit data from a form to another.
 */
class RegistryLoader extends AbstractLoader
{

    /**
     * @param array $values
     * @return array
     */
    public function load(array $values)
    {
        // Fetch data and flush
        $registryValues = $this->getRegistryService()->get('values'); // Hint! use second argument $fetchAndFlush = false to keep values in registry.
        if (is_array($registryValues)) {
            $values = array_merge($values, $registryValues);
        }

        return $values;
    }

    /**
     * @return RegistryService
     */
    protected function getRegistryService()
    {
        return GeneralUtility::makeInstance(RegistryService::class);
    }
}
