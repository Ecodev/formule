<?php
namespace Fab\Formule\Validator;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Interface LoaderInterface
 */
interface ValidatorInterface
{

    /**
     * @param array $values
     * @return array
     */
    public function validate(array $values);
}
