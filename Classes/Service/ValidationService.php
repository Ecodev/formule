<?php
namespace Fab\Formule\Service;

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

use TYPO3\CMS\Core\SingletonInterface;

/**
 * ValidationService
 */
class ValidationService implements SingletonInterface
{
    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @param string $field
     * @return bool
     */
    public function hasErrors($field)
    {
        return isset($this->errors[$field]);
    }

    /**
     * @param string $field
     * @return array
     */
    public function getErrors($field)
    {
        return $this->hasErrors($field) ? $this->errors[$field] : [];
    }

    /**
     * @param string $field
     * @return string
     */
    public function getSerializedErrors($field)
    {
        $errors = '';
        foreach ($this->getErrors($field) as $error) {
            $errors .= $error . ' ';
        }
        return $errors;
    }

    /**
     * @param string $field
     */
    public function addError($field, $message)
    {
        if (empty($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

}