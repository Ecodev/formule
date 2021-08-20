<?php
namespace Fab\Formule\Override\Core\Mail;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Redirect\RedirectService;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Adapter for Swift_Mailer to be used by TYPO3 extensions
 */
class MailMessage extends \TYPO3\CMS\Core\Mail\MailMessage
{

    /**
     * Sends the message.
     */
    public function send(): bool
    {
        $redirectTo = $this->getRedirectService()->redirectionForCurrentContext();

        // Means we want to redirect email.
        if (!empty($redirectTo)) {
            $body = $this->addDebugInfoToBody($this->getBody()->bodyToString());
            $this->setBody()->html($body);
            $this->setTo($redirectTo);
            $this->setCc(array()); // reset cc which was written as debug in the body message previously.
            $this->setBcc(array()); // same remark as bcc.

            $subject = strtoupper((string)Environment::getContext()) . ' CONTEXT! ' . $this->getSubject();
            $this->setSubject($subject);
        }
        return parent::send();
    }

    /**
     * Get a body message when email is not in production.
     *
     * @param string $messageBody
     * @return string
     */
    protected function addDebugInfoToBody($messageBody)
    {
        $to = $this->getTo();
        $cc = $this->getCc();
        $bcc = $this->getBcc();

        $messageBody = sprintf(
            "%s CONTEXT: this message is for testing purposes. In Production, it will be sent as follows. \nto: %s\n%s%s\n%s",
            strtoupper((string)Environment::getContext()),
            implode(',', array_keys($to)),
            empty($cc) ? '' : sprintf("cc: %s \n", implode(',', array_keys($cc))),
            empty($bbc) ? '' : sprintf("bcc: %s \n", implode(',', array_keys($bcc))),
            $messageBody
        );

        return $messageBody;
    }

    /**
     * @return object|RedirectService
     */
    public function getRedirectService()
    {
        return GeneralUtility::makeInstance(RedirectService::class);
    }

}
