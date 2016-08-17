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
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     * @return void
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return string $recipient
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param string $recipient
     * @return void
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * @return string $subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return void
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return string $body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string $attachment
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @param string $attachment
     * @return void
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * @return string $context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param string $context
     * @return void
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return string $wasOpened
     */
    public function getWasOpened()
    {
        return $this->wasOpened;
    }

    /**
     * @param string $wasOpened
     * @return void
     */
    public function setWasOpened($wasOpened)
    {
        $this->wasOpened = $wasOpened;
    }

    /**
     * @return string $sentTime
     */
    public function getSentTime()
    {
        return $this->sentTime;
    }

    /**
     * @param string $sentTime
     * @return void
     */
    public function setSentTime($sentTime)
    {
        $this->sentTime = $sentTime;
    }

    /**
     * @return string $ip
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return void
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

}