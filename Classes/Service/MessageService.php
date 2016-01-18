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

use Fab\Formule\Validator\EmailValidator;
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
    protected $type;

    /**
     * constructor.
     *
     * @param array $settings
     * @param string $messageType
     */
    public function __construct(array $settings, $messageType)
    {
        $this->settings = $settings;
        $this->messageType = $messageType;
    }

    /**
     * @param array $values
     * @return bool
     */
    public function send(array $values)
    {

        $this->values = $values;

        // Substitute markers
        $subject = $this->renderWithFluid($this->getSubject(), $values);
        $body = $this->renderWithFluid($this->getBody(), $values);

        // Parse body through Markdown processing.
        $body = Markdown::defaultTransform($body);

        $this->getMailMessage()->setTo($this->getTo())
            ->setCc($this->getCc())
            ->setBcc($this->getBcc())
            #->setSender($this->getFrom())
            ->setFrom($this->getFrom())
            ->setSubject($subject)
            ->setBody($body, 'text/html');

        // Attach plain text version if HTML tags are found in body
        if ($this->hasHtml($body)) {
            $text = Html2Text::getInstance()->convert($body);
            $this->getMailMessage()->addPart($text, 'text/plain');
        }

        // Handle attachment
        #foreach ($this->attachments as $attachment) {
        #    $this->getMailMessage()->attach($attachment);
        #}

        $this->getMailMessage()->send();
        $isSent = $this->getMailMessage()->isSent();

        $this->getLogginService()->log($this->getMailMessage());
        return $isSent;
    }

    /**
     * Check whether a string contains HTML tags
     *
     * @see http://preprocess.me/how-to-check-if-a-string-contains-html-tags-in-php
     * @param string $content the content to be analyzed
     * @return boolean
     */
    protected function hasHtml($content) {
        $result = FALSE;
        //we compare the length of the string with html tags and without html tags
        if (strlen($content) != strlen(strip_tags($content))) {
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
     * @throws \Exception
     * @throws \Fab\Formule\Exception\InvalidEmailFormatException
     */
    public function getFrom() {

        $from = $this->get('from');
        $from = $this->getEmailAddressService()->parse($from);

        // Compute sender from global configuration.
        if (empty($from)) {
            if (empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'])) {
                throw new \Exception('I could not find a sender email address. Missing value for "defaultMailFromAddress" or define a "from" value in the plugin settings', 1402032685);
            }

            $email = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
            if (empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'])) {
                $name = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'];
            } else {
                $name = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
            }

            $from = array($email => $name);
            $this->getEmailValidator()->validate($from);
        }

        return $from;
    }

    /**
     * @return array
     */
    protected function getTo()
    {
        $to = $this->get('to');

        if(!filter_var($to, FILTER_VALIDATE_EMAIL) && !empty($this->values[$to])) {
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

        if(!filter_var($cc, FILTER_VALIDATE_EMAIL) && !empty($this->values[$cc])) {
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

        if(!filter_var($bcc, FILTER_VALIDATE_EMAIL) && !empty($this->values[$bcc])) {
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
     * @return string
     */
    protected function getBody()
    {
        return $this->get('body');
    }

    /**
     * @return string
     */
    protected function get($key)
    {
        return $this->settings['email' . $this->messageType . ucfirst($key)];
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
     */
    protected function getLogginService()
    {
        return GeneralUtility::makeInstance(LoggingService::class);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
    }

    /**
     * @return EmailAddressService
     */
    public function getEmailAddressService() {
        return GeneralUtility::makeInstance(EmailAddressService::class);
    }

    /**
     * @return EmailValidator
     */
    public function getEmailValidator()
    {
        return GeneralUtility::makeInstance(EmailValidator::class);
    }

}