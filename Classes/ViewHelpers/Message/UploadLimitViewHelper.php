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
