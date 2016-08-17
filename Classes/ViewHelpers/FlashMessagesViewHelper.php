<?php
namespace Fab\Formule\ViewHelpers;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
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
