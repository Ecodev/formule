<?php
declare(strict_types=1);

namespace Fab\Formule\EventListener;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Event\BeforeProcessValuesEvent;

/**
 * Event listener that sanitizes form values before processing
 */
class ValuesSanitizerListener
{
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
