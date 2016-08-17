<?php
namespace Fab\Formule\Processor;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Interface ProcessorInterface
 */
interface ProcessorInterface
{

    const INSERT = 'insert';
    const UPDATE = 'update';

    /**
     * @param array $values
     * @param string $insertOrUpdate
     * @return array
     */
    public function process(array $values, $insertOrUpdate = '');
}
