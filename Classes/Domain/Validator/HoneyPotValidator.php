<?php
namespace Fab\Formule\Domain\Validator;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Service\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validate the honey pot.
 */
class HoneyPotValidator extends AbstractValidator
{

    /**
     * Checks whether:
     *
     * - honeypots are empty (or still have there designated values)
     * - there is a user agent being set
     * - there is a fe_user cookie being set
     *
     * if any of these is wrong it dies with a message.
     *
     * @param mixed $value
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function isValid(mixed $value): void
    {
        // Convert $value to array if it's not already
        $values = is_array($value) ? $value : [];

        if ($this->getTemplateService()->hasHoneyPot()) {

            if (($GLOBALS['TYPO3_REQUEST']->getParsedBody()['mail'] ?? $GLOBALS['TYPO3_REQUEST']->getQueryParams()['mail'] ?? null) || ($GLOBALS['TYPO3_REQUEST']->getParsedBody()['e-mail'] ?? $GLOBALS['TYPO3_REQUEST']->getQueryParams()['e-mail'] ?? null)) {
                die('Looks strange - u sure you are not a bot?');
            }

            if (($GLOBALS['TYPO3_REQUEST']->getParsedBody()['subject2'] ?? $GLOBALS['TYPO3_REQUEST']->getQueryParams()['subject2'] ?? null) !== strrev($GLOBALS['TYPO3_REQUEST']->getParsedBody()['subject3'] ?? $GLOBALS['TYPO3_REQUEST']->getQueryParams()['subject3'] ?? null)) {
                die('Tempered with subject and subject2 - u sure you are not a bot?');
            }

            if (GeneralUtility::getIndpEnv('HTTP_USER_AGENT') == "") {
                die('No user agent - u sure you are not a bot?');
            }
        }
    }

    /**
     * @return object|TemplateService
     * @throws \InvalidArgumentException
     */
    protected function getTemplateService()
    {
        return GeneralUtility::makeInstance(TemplateService::class);
    }

}
