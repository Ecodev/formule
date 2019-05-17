<?php
namespace Fab\Formule\Loader;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * Load user data from session.
 */
class UserDataSessionLoader extends AbstractLoader
{

    /**
     * @param array $values
     * @return array
     */
    public function load(array $values): array
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
