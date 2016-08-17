<?php
namespace Fab\Formule\Validator;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Service\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractValidator
 */
abstract class AbstractValidator implements ValidatorInterface
{
    /**
     * Returns a pointer to the database.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * @return TemplateService
     */
    protected function getTemplateService()
    {
        return GeneralUtility::makeInstance(TemplateService::class);
    }

    /**
     * Returns an instance of the page repository.
     *
     * @return \TYPO3\CMS\Frontend\Page\PageRepository
     */
    protected function getPageRepository()
    {
        return $GLOBALS['TSFE']->sys_page;
    }

}
