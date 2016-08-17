<?php
namespace Fab\Formule\Token;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TokenUtility
 */
class TokenUtility
{

    /**
     * Makes a table tokenisable
     * FOR USE IN ext_localconf.php FILES or files in Configuration/TCA/Overrides/*.php Use the latter to benefit from TCA caching!
     *
     * @param string $extensionKey Extension key to be used
     * @param string $tableName
     * @param string $tokenField
     * @param array $options Additional configuration options
     */
    static public function makeTokenisable($extensionKey, $tableName, $tokenField = 'token', array $options = array()) {
        // Update the Token registry
        $result = TokenRegistry::getInstance()->add($extensionKey, $tableName, $tokenField, $options);
        if ($result === FALSE) {
            $message = 'TokenRegistry: no token registered for table "%s". Key was already registered.';
            /** @var $logger \TYPO3\CMS\Core\Log\Logger */
            $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
            $logger->warning(
                sprintf($message, $tableName)
            );
        }
    }

}