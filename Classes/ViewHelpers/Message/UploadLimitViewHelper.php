<?php
namespace Fab\Formule\ViewHelpers\Message;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class UploadLimitViewHelper
 */
class UploadLimitViewHelper extends AbstractViewHelper
{

    /**
     * @return string
     */
    public function render()
    {
        return 'Max ' . round(GeneralUtility::getMaxUploadFileSize() / 1024, 2)  . ' Mb';
    }
}
