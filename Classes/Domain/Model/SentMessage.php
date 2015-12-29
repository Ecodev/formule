<?php
namespace Fab\Formule\Domain\Model;

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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * SentMessage
 */
class SentMessage extends AbstractEntity
{

    /**
     * sender
     *
     * @var string
     */
    protected $sender = '';

    /**
     * recipient
     *
     * @var string
     */
    protected $recipient = '';

    /**
     * subject
     *
     * @var string
     */
    protected $subject = '';

    /**
     * body
     *
     * @var string
     */
    protected $body = '';

    /**
     * attachment
     *
     * @var string
     */
    protected $attachment = '';

    /**
     * context
     *
     * @var string
     */
    protected $context = '';

    /**
     * wasOpened
     *
     * @var string
     */
    protected $wasOpened = '';

    /**
     * sentTime
     *
     * @var string
     */
    protected $sentTime = '';

    /**
     * ip
     *
     * @var string
     */
    protected $ip = '';

    /**
     * Returns the sender
     *
     * @return string $sender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Sets the sender
     *
     * @param string $sender
     * @return void
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * Returns the recipient
     *
     * @return string $recipient
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Sets the recipient
     *
     * @param string $recipient
     * @return void
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * Returns the subject
     *
     * @return string $subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Sets the subject
     *
     * @param string $subject
     * @return void
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Returns the body
     *
     * @return string $body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Sets the body
     *
     * @param string $body
     * @return void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Returns the attachment
     *
     * @return string $attachment
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * Sets the attachment
     *
     * @param string $attachment
     * @return void
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * Returns the context
     *
     * @return string $context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Sets the context
     *
     * @param string $context
     * @return void
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * Returns the wasOpened
     *
     * @return string $wasOpened
     */
    public function getWasOpened()
    {
        return $this->wasOpened;
    }

    /**
     * Sets the wasOpened
     *
     * @param string $wasOpened
     * @return void
     */
    public function setWasOpened($wasOpened)
    {
        $this->wasOpened = $wasOpened;
    }

    /**
     * Returns the sentTime
     *
     * @return string $sentTime
     */
    public function getSentTime()
    {
        return $this->sentTime;
    }

    /**
     * Sets the sentTime
     *
     * @param string $sentTime
     * @return void
     */
    public function setSentTime($sentTime)
    {
        $this->sentTime = $sentTime;
    }

    /**
     * Returns the ip
     *
     * @return string $ip
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Sets the ip
     *
     * @param string $ip
     * @return void
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

}