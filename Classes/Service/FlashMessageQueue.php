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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * FlashMessageQueue
 */
class FlashMessageQueue implements SingletonInterface
{

    const SUCCESS = 'success';
    const WARNING = 'warning';
    const ERROR = 'danger';

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @param string $message
     */
    public function success($message)
    {
        $this->setMessage(self::SUCCESS, $message);
    }

    /**
     * @param string $message
     */
    public function warning($message)
    {
        $this->setMessage(self::WARNING, $message);
    }

    /**
     * @param string $message
     */
    public function error($message)
    {
        $this->setMessage(self::ERROR, $message);
    }

    /**
     * @param string $severity
     * @param string $message
     */
    protected function setMessage($severity, $message)
    {
        $messages = $this->getMessages();

        if (!is_array($messages)) {
            $messages = [];
        }

        $messages[] = [
            'severity' => $severity,
            'text' => $message
        ];

        $this->getFrontendUser()->setAndSaveSessionData($this->getKey(), $messages);
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->getFrontendUser()->getKey('ses', $this->getKey());
    }

    /**
     * @return array
     */
    public function getMessagesAndFlush()
    {
        $messages = $this->getMessages();

        if (!is_array($messages)) {
            $messages = [];
        }
        $this->getFrontendUser()->setAndSaveSessionData($this->getKey(), []);
        return $messages;
    }

    /**
     * @return string
     */
    protected function getKey()
    {
        return 'formule-flush-messages-' . $this->getTemplateService()->getTemplateIdentifier();
    }

    /**
     * Returns an instance of the current Frontend User.
     *
     * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected function getFrontendUser()
    {
        return $GLOBALS['TSFE']->fe_user;
    }

    /**
     * @return TemplateService
     */
    protected function getTemplateService()
    {
        return GeneralUtility::makeInstance(TemplateService::class);
    }

}