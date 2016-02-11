<?php
namespace Fab\Formule\Loader;

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

/**
 * Load user data from session.
 */
class UserDataSessionLoader extends AbstractLoader
{

    /**
     * @param array $values
     * @param string $insertOrUpdate
     * @return array
     */
    public function load(array $values, $insertOrUpdate = '')
    {
        $userData = $this->getFrontendUser()->user;

        if (!empty($userData)) {
            $values = array_merge($values, $userData);
        }

        return $values;
    }

    /**
     * Returns an instance of the current Frontend User.
     *
     * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected function getFrontendUser()
    {
        return $GLOBALS['TSFE']->fe_user;
    }
}
