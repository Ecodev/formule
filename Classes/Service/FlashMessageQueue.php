<?php
namespace Fab\Formule\Service;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

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

    protected function getFrontendUser(): FrontendUserAuthentication
    {
        return $GLOBALS['TSFE']->fe_user;
    }

    protected function getTemplateService(): TemplateService
    {
        return GeneralUtility::makeInstance(TemplateService::class);
    }

}
