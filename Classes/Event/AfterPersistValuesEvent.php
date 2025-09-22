<?php
declare(strict_types=1);

namespace Fab\Formule\Event;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Event that is triggered after persisting form values
 */
class AfterPersistValuesEvent implements StoppableEventInterface
{
    private array $values;
    private bool $propagationStopped = false;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
