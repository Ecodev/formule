<?php
namespace Fab\Formule\ViewHelpers;

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

use Fab\Formule\Service\FlashMessageQueue;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which returns flush messages.
 */
class FlashMessagesViewHelper extends AbstractViewHelper
{

    /**
     * @return array
     */
    public function render()
    {
        $messages = $this->getFlashMessageQueue()->getMessagesAndFlush();
        return $messages;
    }

    /**
     * @return FlashMessageQueue
     */
    protected function getFlashMessageQueue()
    {
        return GeneralUtility::makeInstance(FlashMessageQueue::class);
    }

}
