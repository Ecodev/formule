<?php

namespace Fab\Formule\Service;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Michelf\Markdown;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use Symfony\Component\Mime\Part\TextPart;
use Symfony\Component\Mime\Part\Multipart\AlternativePart;
use Fab\Formule\Service\LoggingService;
use Psr\Container\ContainerInterface;

/**
 * MessageService
 */
class MessageService
{
    protected LoggingService $loggingService;

    public function __construct(array $settings, string $messageTarget, ?LoggingService $loggingService = null)
    {
        $this->settings = $settings;
        $this->messageTarget = $messageTarget;

        if ($loggingService !== null) {
            $this->loggingService = $loggingService;
        } else {
            // Create LoggingService with its required dependencies
            $connectionPool = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class);
            $this->loggingService = new LoggingService($connectionPool);
        }
    }

    const TO_ADMIN = 'Admin';

    const TO_USER = 'User';

    /**
     * @var
     */
    protected $mailMessage;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var array
     */
    protected $values;

    /**
     * @var string
     */
    protected $messageTarget;


    /**
     * @param array $values
     * @return bool
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function send(array $values): bool
    {
        $this->values = $values;

        // Substitute markers
        $subject = $this->renderWithFluid($this->getSubject(), $values);
        $body = $this->renderWithFluid($this->getBody(), $values);

        // Parse body through Markdown processing.
        $body = Markdown::defaultTransform($body);

        $this->getMailMessage()
            #->setSender($this->getFrom())
            ->setFrom($this->getFrom())
            ->setTo($this->getTo())
            #->setCc($this->getCc())
            #->setBcc($this->getBcc())
            ->setSubject($subject);

        // According to preference.
        if ($this->isPlainTextPreferred()) {
            $text = Html2Text::getInstance()->convert($body);
            $this->getMailMessage()->setBody(new TextPart($text));
        } else {
            // Create multipart message with HTML and text versions
            if ($this->hasHtml($body)) {
                $text = Html2Text::getInstance()->convert($body);
                $alternativePart = new AlternativePart(
                    new TextPart($text),
                    new TextPart($body, 'text/html')
                );
                $this->getMailMessage()->setBody($alternativePart);
            } else {
                $this->getMailMessage()->setBody(new TextPart($body, 'text/html'));
            }
        }

        // Handle attachment
        #foreach ($this->attachments as $attachment) {
        #    $this->getMailMessage()->attach($attachment);
        #}

        $this->getMailMessage()->send();
        $isSent = $this->getMailMessage()->isSent();

        $this->getLoggingService()->log($this->getMailMessage());
        return $isSent;
    }

    /**
     * Check whether a string contains HTML tags
     *
     * @see http://preprocess.me/how-to-check-if-a-string-contains-html-tags-in-php
     * @param string $content the content to be analyzed
     * @return boolean
     */
    protected function hasHtml(string $content): bool
    {
        $result = FALSE;
        //we compare the length of the string with html tags and without html tags
        if (strlen($content) !== strlen(strip_tags($content))) {
            $result = TRUE;
        }
        return $result;
    }

    /**
     * @param $content
     * @param array $values
     * @return string
     */
    protected function renderWithFluid($content, array $values): string
    {
        /** @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplateSource($content);

        // Make it possible to keen the syntact {values.foo} in the template
        $values['values'] = $values;

        $view->assignMultiple($values);
        return trim($view->render());
    }

    /**
     * @return array
     * @throws \Fab\Formule\Exception\InvalidEmailFormatException
     */
    public function getFrom(): array
    {

        $emailFrom = [];
        if (!empty($this->settings['emailFrom'])) {

            $email = $this->settings['emailFrom'];
            if (empty($this->settings['nameFrom'])) {
                $name = $this->settings['emailFrom'];
            } else {
                $name = $this->settings['nameFrom'];
            }

            $emailFrom = array($email => $name);
            $this->getEmailAddressService()->validate($emailFrom);
        } elseif (!empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'])) {

            $email = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
            if (empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'])) {
                $name = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'];
            } else {
                $name = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
            }

            $emailFrom = array($email => $name);
            $this->getEmailAddressService()->validate($emailFrom);
        }

        return $emailFrom;
    }

    /**
     * @return array
     */
    protected function getTo(): array
    {
        $to = $this->get('to');

        if (!filter_var($to, FILTER_VALIDATE_EMAIL) && !empty($this->values[$to])) {
            $to = $this->values[$to];
        }

        return $this->getEmailAddressService()->parse($to);
    }

    /**
     * @return array
     */
    protected function getCc(): array
    {
        $cc = $this->get('cc');

        if (!filter_var($cc, FILTER_VALIDATE_EMAIL) && !empty($this->values[$cc])) {
            $cc = $this->values[$cc];
        }

        return $this->getEmailAddressService()->parse($cc);
    }

    /**
     * @return array
     */
    protected function getBcc(): array
    {
        $bcc = $this->get('bcc');

        if (!filter_var($bcc, FILTER_VALIDATE_EMAIL) && !empty($this->values[$bcc])) {
            $bcc = $this->values[$bcc];
        }

        return $this->getEmailAddressService()->parse($bcc);
    }

    /**
     * @return string
     */
    protected function getSubject(): string
    {
        return $this->get('subject');
    }

    /**
     * @return bool
     */
    protected function isPlainTextPreferred(): bool
    {
        return $this->getTemplateService($this->settings['template'])->getPreferredEmailBodyEncoding() === 'text';
    }

    /**
     * @return string
     */
    protected function getBody(): string
    {
        $section = $this->messageTarget === self::TO_ADMIN ? TemplateService::SECTION_EMAIL_ADMIN : TemplateService::SECTION_EMAIL_USER;
        $body = $this->getTemplateService($this->settings['template'])->getSection($section);
        if (empty($body)) {
            $body = $this->get('body');
        }
        return $body;
    }

    /**
     * @return string
     */
    protected function get($key): string
    {
        return $this->settings['email' . $this->messageTarget . ucfirst($key)];
    }

    /**
     * @return object|MailMessage
     */
    protected function getMailMessage()
    {
        if (is_null($this->mailMessage)) {
            $this->mailMessage = GeneralUtility::makeInstance(MailMessage::class);
        }
        return $this->mailMessage;
    }

    /**
     * @return object|LoggingService
     * @throws \InvalidArgumentException
     */
    protected function getLoggingService()
    {
        return $this->loggingService;
    }

    /**
     * @return object|EmailAddressService
     * @throws \InvalidArgumentException
     */
    public function getEmailAddressService()
    {
        return GeneralUtility::makeInstance(EmailAddressService::class);
    }

    /**
     * @param int $templateIdentifier
     * @return object|TemplateService
     * @throws \InvalidArgumentException
     */
    protected function getTemplateService(int $templateIdentifier)
    {
        return GeneralUtility::makeInstance(TemplateService::class, $templateIdentifier);
    }

}
