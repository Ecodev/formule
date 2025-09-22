<?php
namespace Fab\Formule\Domain\Model;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * SentMessage
 */
class SentMessage extends AbstractEntity
{

    /**
     * @var string
     */
    protected $sender = '';

    /**
     * @var string
     */
    protected $recipient = '';

    /**
     * @var string
     */
    protected $subject = '';

    /**
     * @var string
     */
    protected $body = '';

    /**
     * @var string
     */
    protected $attachment = '';

    /**
     * @var string
     */
    protected $context = '';

    /**
     * @var string
     */
    protected $wasOpened = '';

    /**
     * @var string
     */
    protected $sentTime = '';

    /**
     * @var string
     */
    protected $ip = '';

    /**
     * @return string $sender
     */
    public function getSender(): string
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     * @return void
     */
    public function setSender(string $sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return string $recipient
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * @param string $recipient
     * @return void
     */
    public function setRecipient(string $recipient): void
    {
        $this->recipient = $recipient;
    }

    /**
     * @return string $subject
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return void
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string $body
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return void
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string $attachment
     */
    public function getAttachment(): string
    {
        return $this->attachment;
    }

    /**
     * @param string $attachment
     * @return void
     */
    public function setAttachment(string $attachment): void
    {
        $this->attachment = $attachment;
    }

    /**
     * @return string $context
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * @param string $context
     * @return void
     */
    public function setContext(string $context): void
    {
        $this->context = $context;
    }

    /**
     * @return string $wasOpened
     */
    public function getWasOpened(): string
    {
        return $this->wasOpened;
    }

    /**
     * @param string $wasOpened
     * @return void
     */
    public function setWasOpened(string $wasOpened): void
    {
        $this->wasOpened = $wasOpened;
    }

    /**
     * @return string $sentTime
     */
    public function getSentTime(): string
    {
        return $this->sentTime;
    }

    /**
     * @param string $sentTime
     * @return void
     */
    public function setSentTime(string $sentTime): void
    {
        $this->sentTime = $sentTime;
    }

    /**
     * @return string $ip
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return void
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

}
