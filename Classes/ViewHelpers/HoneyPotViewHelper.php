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

use FluidTYPO3\Vhs\Asset;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper to some honey pot field.
 */
class HoneyPotViewHelper extends AbstractViewHelper
{

    /**
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception\InvalidVariableException
     */
    public function render()
    {
        return '
        <div style="display: none;">
			<label>
				<input type="text" name="mail"/>
			</label>
			<label>
				<input type="text" name="e-mail"/>
			</label>
			<label>
				<input type="text" name="subject2" value="contact form"/>
			</label>
			<label>
				<input type="text" name="subject3" value="mrof tcatnoc"/>
			</label>
		</div>
        ';
    }

}
