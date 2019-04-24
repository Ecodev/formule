<?php
namespace Fab\Formule\ViewHelpers;

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
 * View helper which tells whether the field contains possible errors
 */
class HasErrorViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('field', 'string', '', true);
    }

    /**
     * @return string
     */
    public function render()
    {
        $field = $this->arguments['field'];
        return $this->getValidationService()->hasErrors($field) ? 'has-error' : '';
    }

    /**
     * @return ValidationService|object
     */
    protected function getValidationService()
    {
        return GeneralUtility::makeInstance(ValidationService::class);
    }

}
