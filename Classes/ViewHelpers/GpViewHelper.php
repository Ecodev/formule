<?php
namespace Fab\Formule\ViewHelpers;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which returns GP value
 */
class GpViewHelper extends AbstractViewHelper
{

    /**
     * @param string $key
     * @return string
     */
    public function render($key)
    {
        return GeneralUtility::_GP($key);
    }

}
