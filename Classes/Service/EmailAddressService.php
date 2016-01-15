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

use TYPO3\CMS\Core\SingletonInterface;

/**
 * EmailAddressService
 */
class EmailAddressService implements SingletonInterface
{

    /**
     * @param string $listOfEmails
     * @return array
     */
    public function parse($listOfEmails)
    {
        $emails = array();

        if (preg_match_all('/\s*"?([^><,"]+)"?\s*((?:<[^><,]+>)?)\s*/', $listOfEmails, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                if (!empty($match[2])) {
                    $emails[trim($match[2], '<>')] = $match[1];
                } else {
                    $emails[$match[1]] = $match[1];
                }
            }
        }
        return $emails;
    }

}