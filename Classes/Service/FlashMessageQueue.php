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
     * In-memory fallback when no frontend user session is available (e.g. CLI or before TSFE init).
     *
     * @var array<string, array<int, array{severity: string, text: string}>>
     */
    private static $memoryFallback = [];

    /**
     * @param string $message
     */
    public function success(string $message): void
    {
        $this->setMessage(self::SUCCESS, $message);
    }

    /**
     * @param string $message
     */
    public function warning(string $message): void
    {
        $this->setMessage(self::WARNING, $message);
    }

    /**
     * @param string $message
     */
    public function error(string $message): void
    {
        $this->setMessage(self::ERROR, $message);
    }

    /**
     * @param string $severity
     * @param string $message
     */
    protected function setMessage(string $severity, string $message)
    {
        $messages = $this->getMessages();

        if (!is_array($messages)) {
            $messages = [];
        }

        $messages[] = [
            'severity' => $severity,
            'text' => $message
        ];

        $feUser = $this->getFrontendUser();
        if ($feUser !== null) {
            $feUser->setAndSaveSessionData($this->getKey(), $messages);
        } else {
            self::$memoryFallback[$this->getKey()] = $messages;
        }
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        $feUser = $this->getFrontendUser();
        if ($feUser !== null) {
            return $feUser->getKey('ses', $this->getKey()) ?? [];
        }
        return self::$memoryFallback[$this->getKey()] ?? [];
    }

    /**
     * @return array
     */
    public function getMessagesAndFlush(): array
    {
        $messages = $this->getMessages();
        $feUser = $this->getFrontendUser();
        if ($feUser !== null) {
            $feUser->setAndSaveSessionData($this->getKey(), []);
        } else {
            $key = $this->getKey();
            if (isset(self::$memoryFallback[$key])) {
                unset(self::$memoryFallback[$key]);
            }
        }
        return $messages;
    }

    /**
     * @return string
     */
    protected function getKey(): string
    {
        return 'formule-flush-messages-' . $this->getTemplateService()->getTemplateIdentifier();
    }

    protected function getFrontendUser(): ?FrontendUserAuthentication
    {
        $tsfe = $GLOBALS['TSFE'] ?? null;
        if (!is_object($tsfe) || !isset($tsfe->fe_user)) {
            return null;
        }
        $feUser = $tsfe->fe_user;
        return $feUser instanceof FrontendUserAuthentication ? $feUser : null;
    }

    protected function getTemplateService(): TemplateService
    {
        return GeneralUtility::makeInstance(TemplateService::class);
    }

}
