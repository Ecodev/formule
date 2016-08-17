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
     * @param array $values
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function isValid($values)
    {

        if ($this->getTemplateService()->hasHoneyPot()) {

            if (GeneralUtility::_GP('mail') || GeneralUtility::_GP('e-mail')) {
                die('Looks strange - u sure you are not a bot?');
            }

            if (GeneralUtility::_GP('subject2') !== strrev(GeneralUtility::_GP('subject3'))) {
                die('Tempered with subject and subject2 - u sure you are not a bot?');
            }

            if (GeneralUtility::getIndpEnv('HTTP_USER_AGENT') == "") {
                die('No user agent - u sure you are not a bot?');
            }
        }
    }

    /**
     * @return TemplateService
     * @throws \InvalidArgumentException
     */
    protected function getTemplateService()
    {
        return GeneralUtility::makeInstance(TemplateService::class);
    }

}
