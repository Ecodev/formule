<?php
namespace Fab\Formule\ViewHelpers\Context;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which tells whether we are in development context
 */
class IsDevelopmentViewHelper extends AbstractViewHelper
{

    /**
     * @return string
     */
    public function render () {
        return GeneralUtility::getApplicationContext()->isDevelopment();
    }

}
