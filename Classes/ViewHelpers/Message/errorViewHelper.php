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
