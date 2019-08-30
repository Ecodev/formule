<?php
namespace Fab\Formule\Service;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
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

        $queryBuilder = $this->getQueryBuilder($tableName);

        $values = [
            'pid' => (int)$this->getFrontendObject()->id,
            'sender' => $this->formatEmails($message->getFrom()),
            'recipient' => $this->formatEmails($message->getTo()),
            'recipient_cc' => $this->formatEmails($message->getCc()),
            'recipient_bcc' => $this->formatEmails($message->getBcc()),
            'subject' => $queryBuilder->quote($message->getSubject(), $tableName),
            'body' => $queryBuilder->quote($message->getBody(), $tableName),
            #'attachment' => '',
            'context' => (string)GeneralUtility::getApplicationContext(),
            'is_sent' => (int)$message->isSent(),
            'sent_time' => time(),
            'ip' => GeneralUtility::getIndpEnv('REMOTE_ADDR'),
            'crdate' => time(),
        ];

        $queryBuilder
            ->insert($tableName)
            ->values($values)
            ->execute();
    }

    /**
     * @param string $tableName
     * @return object|QueryBuilder
     */
    protected function getQueryBuilder($tableName): QueryBuilder
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $connectionPool->getQueryBuilderForTable($tableName);
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
     * Returns an instance of the Frontend object.
     *
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getFrontendObject()
    {
        return $GLOBALS['TSFE'];
    }
}
