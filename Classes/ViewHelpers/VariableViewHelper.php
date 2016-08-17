<?php
namespace Fab\Formule\ViewHelpers;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Service\FlashMessageQueue;
use Fab\Formule\Service\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which returns variables.
 */
class VariableViewHelper extends AbstractViewHelper
{

    /**
     * @param string $name
     * @return array
     */
    public function render($name)
    {
        return $this->getTemplateService()->getVariable($name);
    }

    /**
     * @return TemplateService
     */
    protected function getTemplateService()
    {
        return GeneralUtility::makeInstance(TemplateService::class);
    }

}
