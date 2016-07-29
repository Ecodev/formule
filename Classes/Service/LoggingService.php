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

use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * LoggingService
 */
class LoggingService
{

    /**
     * @param MailMessage $message
     * @throws \UnexpectedValueException
     * @throws \BadFunctionCallException
     */
    public function log(MailMessage $message)
    {

        $tableName = ExtensionManagementUtility::isLoaded('messenger')
            ? 'tx_messenger_domain_model_sentmessage'
            : 'tx_formule_domain_model_sentmessage';

        $values = [
            'pid' => (int)$this->getFrontendObject()->id,
            'sender' => $this->formatEmails($message->getFrom()),
            'recipient' => $this->formatEmails($message->getTo()),
            'recipient_cc' => $this->formatEmails($message->getCc()),
            'recipient_bcc' => $this->formatEmails($message->getBcc()),
            'subject' => $this->getDatabaseConnection()->quoteStr($message->getSubject(), $tableName),
            'body' => $this->getDatabaseConnection()->quoteStr($message->getBody(), $tableName),
            #'attachment' => '',
            'context' => (string)GeneralUtility::getApplicationContext(),
            'is_sent' => (int)$message->isSent(),
            'sent_time' => time(),
            'ip' => GeneralUtility::getIndpEnv('REMOTE_ADDR'),
            'crdate' => time(),
        ];

        $this->getDatabaseConnection()->exec_INSERTquery($tableName, $values);
    }

    /**
     * @param array|null $emails
     * @return string
     */
    protected function formatEmails($emails)
    {
        $formattedEmails = '';
        if (is_array($emails)) {
            $formattedEmails = implode(', ', array_keys($emails));
        }
        return $formattedEmails;
    }

    /**
     * Returns a pointer to the database.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Returns an instance of the Frontend object.
     *
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getFrontendObject()
    {
        return $GLOBALS['TSFE'];
    }
}