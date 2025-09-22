<?php
namespace Fab\Formule\Service;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
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
    public function hasErrors(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * @param string $field
     * @return array
     */
    public function getErrors(string $field): array
    {
        return $this->hasErrors($field) ? $this->errors[$field] : [];
    }

    /**
     * @param string $field
     * @return string
     */
    public function getSerializedErrors(string $field): string
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
    public function addError(string $field, $message): void
    {
        if (empty($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

}
