<?php
namespace Fab\Formule\Service;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Exception\InvalidEmailFormatException;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * EmailAddressService
 */
class EmailAddressService implements SingletonInterface
{

    /**
     * @param string $listOfEmails
     * @return array
     */
    public function parse($listOfEmails)
    {
        $emails = array();

        if (preg_match_all('/\s*"?([^><,"]+)"?\s*((?:<[^><,]+>)?)\s*/', $listOfEmails, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                if (!empty($match[2])) {
                    $emails[trim($match[2], '<>')] = $match[1];
                } else {
                    $emails[$match[1]] = $match[1];
                }
            }
        }
        return $emails;
    }

    /**
     * Validate emails to be used in the SwiftMailer framework
     *
     * @throws InvalidEmailFormatException
     * @param $emails
     * @return boolean
     */
    public function validate($emails)
    {
        foreach ($emails as $email => $name) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = sprintf('Email provided is not valid, given value "%s"', $email);
                throw new InvalidEmailFormatException($message, 1350297165);
            }
            if (strlen($name) <= 0) {
                $message = sprintf('Name should not be empty, given value "%s"', $name);
                throw new InvalidEmailFormatException($message, 1350297170);
            }
        }
        return true;
    }

}