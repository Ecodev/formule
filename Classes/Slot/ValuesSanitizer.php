<?php
namespace Fab\Formule\Slot;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */


/**
 * ValuesSanitizer
 */
class ValuesSanitizer
{

    /**
     * @param array $values
     * @return array
     */
    public function sanitize(array $values)
    {
        foreach ($values as $key => $value) {
            if (is_scalar($value)) {
                $values[$key] = trim($value);
            }
        }

        return [$values];
    }

}