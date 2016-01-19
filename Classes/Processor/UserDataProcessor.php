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
 * Class UserDataProcessor
 */
class UserDataProcessor extends AbstractProcessor
{

    /**
     * @param array $values
     * @param string $insertOrUpdate
     * @return array
     */
    public function process(array $values, $insertOrUpdate = '')
    {

        $values['name'] = $values['first_name'] . ' ' . $values['last_name'];

        if ($insertOrUpdate === ProcessorInterface::INSERT) {
            $values['username'] = ''; // todo
            $values['password'] = ''; // todo
            #$values['token'] = ''; // todo
        }
        return $values;
    }
}
