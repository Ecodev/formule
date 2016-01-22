<?php
namespace Fab\Formule\Processor;

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

/**
 * Interface InterceptorInterface
 */
interface InterceptorInterface
{

    const INSERT = 'insert';
    const UPDATE = 'update';

    /**
     * @param array $values
     * @param string $insertOrUpdate
     * @return array
     */
    public function intercept(array $values, $insertOrUpdate = '');
}
