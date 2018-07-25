<?php
namespace Fab\Formule\ViewHelpers;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper to some honey pot field.
 */
class HoneyPotViewHelper extends AbstractViewHelper
{

    /**
     * @var bool
     */
    protected $escapeOutput = false;

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
