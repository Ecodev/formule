<?php
namespace Fab\Formule\ViewHelpers\Message;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Service\ValidationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which returns message errors.
 */
class ErrorViewHelper extends AbstractViewHelper
{

    /**
     * @param string $field
     * @return string
     */
    public function render($field)
    {
        return $this->getValidationService()->getSerializedErrors($field);
    }

    /**
     * @return ValidationService
     */
    protected function getValidationService()
    {
        return GeneralUtility::makeInstance(ValidationService::class);
    }

}
