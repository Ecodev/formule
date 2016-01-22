<?php
namespace Fab\Formule\ViewHelpers\Message;

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

use Fab\Formule\Redirect\RedirectService;
use Fab\Formule\Service\EmailAddressService;
use Fab\Formule\Service\FlexFormService;
use Fab\Formule\Service\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper to some honey pot field.
 */
class DevelopmentViewHelper extends AbstractViewHelper
{

    /**
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception\InvalidVariableException
     * @return string
     */
    public function render()
    {
        $redirectTo = $this->getRedirectService()->redirectionForCurrentContext();
        $output = '';

        // Means we want to redirect email.
        if (!empty($redirectTo)) {
            $contentElement = $this->templateVariableContainer->get('contentElement');
            $settings = $this->getFlexFormService()->extractSettings($contentElement['pi_flexform']);
            $to = $this->getEmailAddressService()->parse($settings['emailAdminTo']);
            #$cc = $this->getEmailAddressService()->parse($settings['emailAdminCc']);
            #$bcc = $this->getEmailAddressService()->parse($settings['emailAdminBcc']);

            $templateService = $this->getTemplateService($settings['template']);

            $output = sprintf(
                "<pre style='clear: both'>%s CONTEXT<br /> %s %s %s %s %s</pre>",
                strtoupper((string)GeneralUtility::getApplicationContext()),
                $this->hasEmails($settings) ? '<br />- All emails will be redirected to ' . implode(', ', array_keys($redirectTo)) . '.' : '',
                empty($to) ? '' : '<br />- Admin email will be sent to: ' . implode(', ', array_keys($to)),
                #empty($cc) ? '' : sprintf('<br/>    - cc: %s', implode(', ', array_keys($cc))),
                #empty($bcc) ? '' : sprintf('<br/>    - bcc: %s', implode(', ', array_keys($bcc))),
                empty($settings['emailUserTo']) ? '' : '<br/>- User email will be sent using the field "' . $settings['emailUserTo'] . '"',
                $this->isSenderOk($settings) ? '' : '<br/>- ATTENTION! No sender could be found. This will be a problem when sending emails.',
                $templateService->hasPersistingTable() ? '<br/>- Submitted data will be persisted into "' . $templateService->getPersistingTable() . '"' : ''
            );
        }

        return $output;
    }

    /**
     * @param array $settings
     * @return bool
     */
    public function hasEmails(array $settings) {
        return !empty($settings['emailAdminTo']) && !empty($settings['emailUserTo']);
    }

    /**
     * @param array $settings
     * @return bool
     */
    public function isSenderOk(array $settings) {
        $isOk = true;
        if ($this->hasEmails($settings)) {
            $isOk = !empty($settings['emailFrom']) || !empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress']);
        }
        return $isOk;
    }

    /**
     * @return RedirectService
     */
    public function getRedirectService() {
        return GeneralUtility::makeInstance(RedirectService::class);
    }

    /**
     * @return EmailAddressService
     */
    public function getEmailAddressService() {
        return GeneralUtility::makeInstance(EmailAddressService::class);
    }

    /**
     * @return FlexFormService
     */
    protected function getFlexFormService()
    {
        return GeneralUtility::makeInstance(FlexFormService::class);
    }

    /**
     * @param int $templateIdentifier
     * @return TemplateService
     */
    protected function getTemplateService($templateIdentifier)
    {
        return GeneralUtility::makeInstance(TemplateService::class, $templateIdentifier);
    }

}
