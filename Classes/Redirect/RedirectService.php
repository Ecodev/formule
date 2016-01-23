<?php
namespace Fab\Formule\Redirect;

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

use Fab\Formule\Service\EmailAddressService;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Formule\Utility\ConfigurationUtility;

/**
 * Class providing service for redirection of emails
 */
class RedirectService implements SingletonInterface
{

    /**
     * Get possible redirect recipients.
     *
     * @return array
     */
    public function redirectionForCurrentContext()
    {
        // Fetch email from PHP configuration array at first.
        $applicationContext = strtolower((string)GeneralUtility::getApplicationContext()->getParent());
        $key = $applicationContext . '_redirect_to';
        if (isset($GLOBALS['TYPO3_CONF_VARS']['MAIL'][$key])) {
            $recipientList = $GLOBALS['TYPO3_CONF_VARS']['MAIL'][$key];
        } else {
            $recipientList = ConfigurationUtility::getInstance()->get($key);
        }

        $recipients = array();
        if (strlen(trim($recipientList)) > 0) {
            $emails = GeneralUtility::trimExplode(',', $recipientList);

            foreach ($emails as $email) {
                $recipients[$email] = $email;
            }

            $this->getEmailAddressService()->validate($recipients);
        }

        return $recipients;
    }

    /**
     * @return EmailAddressService
     */
    public function getEmailAddressService()
    {
        return GeneralUtility::makeInstance(EmailAddressService::class);
    }

}
