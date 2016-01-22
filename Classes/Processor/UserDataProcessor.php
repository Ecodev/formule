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

use Fab\Formule\Service\TemplateService;
use Fab\Formule\Token\TokenRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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

        #$values['name'] = $values['first_name'] . ' ' . $values['last_name'];

        if ($insertOrUpdate === ProcessorInterface::INSERT) {
            #$values['username'] = ''; // todo must be unique
            #$values['password'] = ''; // todo must be salted

            if ($this->getTemplateService()->hasPersistingTable()) {

                $tableName = $this->getTemplateService()->getPersistingTable();
                $isRegistered = TokenRegistry::getInstance()->isRegistered($tableName);
                if ($isRegistered) {
                    $tokenField = TokenRegistry::getInstance()->getTokenField($tableName);
                    $values[$tokenField] = $this->getUuid();
                }
            }
        }

        return $values;
    }

    /**
     * @see http://php.net/manual/en/function.uniqid.php#94959
     * @return string
     */
    protected function getUuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * @return TemplateService
     */
    protected function getTemplateService()
    {
        return GeneralUtility::makeInstance(TemplateService::class);
    }

}
