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

use Michelf\Markdown;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * MessageService
 */
class MessageService
{

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
     * constructor.
     *
     * @param array $settings
     * @param string $messageTarget
     */
    public function __construct(array $settings, $messageTarget)
    {
        $this->settings = $settings;
        $this->messageTarget = $messageTarget;
    }

    /**
     * @param array $values
     * @return bool
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function send(array $values)
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
            $this->getMailMessage()->setBody($text);
        } else {
            $this->getMailMessage()->setBody($body, 'text/html');

            // Attach plain text version if HTML tags are found in body
            if ($this->hasHtml($body)) {
                $text = Html2Text::getInstance()->convert($body);
                $this->getMailMessage()->addPart($text, 'text/plain');
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
    protected function hasHtml($content)
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
    protected function renderWithFluid($content, array $values)
    {
        /** @var StandaloneView $view */
        $view = $this->getObjectManager()->get(StandaloneView::class);
        $view->setTemplateSource($content);

        $view->assignMultiple($values);
        return trim($view->render());
    }

    /**
     * @return array
     * @throws \Fab\Formule\Exception\InvalidEmailFormatException
     */
    public function getFrom()
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
    protected function getTo()
    {
        $to = $this->get('to');

        if (!filter_var($to, FILTER_VALIDATE_EMAIL) && !empty($this->values[$to])) {
            $to = $this->values[$to];
        }

        return $this->getEmailAddressService()->parse($to);
    }

    /**
     * @return string
     */
    protected function getCc()
    {
        $cc = $this->get('cc');

        if (!filter_var($cc, FILTER_VALIDATE_EMAIL) && !empty($this->values[$cc])) {
            $cc = $this->values[$cc];
        }

        return $this->getEmailAddressService()->parse($cc);
    }

    /**
     * @return string
     */
    protected function getBcc()
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
    protected function getSubject()
    {
        return $this->get('subject');
    }

    /**
     * @return bool
     */
    protected function isPlainTextPreferred()
    {
        return $this->getTemplateService($this->settings['template'])->getPreferredEmailBodyEncoding() === 'text';
    }

    /**
     * @return string
     */
    protected function getBody()
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
    protected function get($key)
    {
        return $this->settings['email' . $this->messageTarget . ucfirst($key)];
    }

    /**
     * @return MailMessage
     */
    protected function getMailMessage()
    {
        if (is_null($this->mailMessage)) {
            $this->mailMessage = $this->getObjectManager()->get(MailMessage::class);
        }
        return $this->mailMessage;
    }

    /**
     * @return LoggingService
     * @throws \InvalidArgumentException
     */
    protected function getLoggingService()
    {
        return GeneralUtility::makeInstance(LoggingService::class);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Object\ObjectManager
     * @throws \InvalidArgumentException
     */
    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
    }

    /**
     * @return EmailAddressService
     * @throws \InvalidArgumentException
     */
    public function getEmailAddressService()
    {
        return GeneralUtility::makeInstance(EmailAddressService::class);
    }

    /**
     * @param int $templateIdentifier
     * @return TemplateService
     * @throws \InvalidArgumentException
     */
    protected function getTemplateService($templateIdentifier)
    {
        return GeneralUtility::makeInstance(TemplateService::class, $templateIdentifier);
    }

}