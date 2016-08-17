<?php
namespace Fab\Formule\Html2Text;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Strategy Interface for converting HTML to text.
 */
interface StrategyInterface {

	/**
	 * Convert a given HTML input to Text
	 *
	 * @param string $input
	 * @return string
	 */
	public function convert($input);

	/**
	 * Whether the converter is available
	 *
	 * @return boolean
	 */
	public function available();
}
