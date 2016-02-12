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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Saltedpasswords\Salt\SaltFactory;
use TYPO3\CMS\Saltedpasswords\Utility\SaltedPasswordsUtility;

/**
 * Class UserDataProcessor.
 * Only an example class to be copy / pasted and adjusted!!!
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
            $values['username'] = $values['email'];
            $values['password'] = $this->getSaltedPassword($values['password']);
            #$values['token'] = $this->getUuid(); // fields to be created...
            #$values['is_confirmed'] = 0;
            #$values['is_subscribed'] = 1;
        } else {
            if (empty($values['password'])) {
                unset($values['password']);
            } else {
                $values['password'] = $this->getSaltedPassword($values['password']);
            }
        }

        return $values;
    }

    /**
     * @return string
     */
    protected function getSaltedPassword($password)
    {
        $saltedPassword = $password;
        if (ExtensionManagementUtility::isLoaded('saltedpasswords')) {
            if (SaltedPasswordsUtility::isUsageEnabled('FE')) {
                $objSalt = SaltFactory::getSaltingInstance(NULL);
                if (is_object($objSalt)) {
                    $saltedPassword = $objSalt->getHashedPassword($password);
                }
            }
        }
        return $saltedPassword;
    }

    /**
     * @see http://php.net/manual/en/function.uniqid.php#94959
     * @return string
     */
    //protected function getUuid()
    //{
    //    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    //        // 32 bits for "time_low"
    //        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
    //
    //        // 16 bits for "time_mid"
    //        mt_rand(0, 0xffff),
    //
    //        // 16 bits for "time_hi_and_version",
    //        // four most significant bits holds version number 4
    //        mt_rand(0, 0x0fff) | 0x4000,
    //
    //        // 16 bits, 8 bits for "clk_seq_hi_res",
    //        // 8 bits for "clk_seq_low",
    //        // two most significant bits holds zero and one for variant DCE1.1
    //        mt_rand(0, 0x3fff) | 0x8000,
    //
    //        // 48 bits for "node"
    //        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    //    );
    //}

}
