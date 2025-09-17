<?php
namespace Fab\Formule\Slot;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Event\BeforeProcessValuesEvent;

/**
 * ValuesSanitizer
 */
class ValuesSanitizer
{

    /**
     * @param BeforeProcessValuesEvent $event
     */
    public function sanitize(BeforeProcessValuesEvent $event): void
    {
        $values = $event->getValues();
        
        foreach ($values as $key => $value) {
            if (is_scalar($value)) {
                $values[$key] = trim($value);
            }
        }

        $event->setValues($values);
    }

}