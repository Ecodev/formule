<?php
namespace Fab\Formule\ViewHelpers\Link;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Service\TemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper to create a confirm link.
 */
class ConfirmViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('pageUid', 'string', '', true);
    }

    /**
     * @return string
     */
    public function render()
    {
        $pageUid = $this->arguments['pageUid'];
        // Render inner content
        $content = $this->renderChildren();

        $pageUid = $this->resolvePageUid($pageUid);
        if ($content) {
            $link = sprintf('<a href="%s">%s</a>', $this->getUrl($pageUid), $content);
        } else {
            $link = $this->getUrl($pageUid);
        }

        return $link;
    }

    /**
     * @param string|int $pageUid
     * @return int
     */
    protected function resolvePageUid($pageUid)
    {
        $resolvedPageUid = $this->getTemplateService()->getVariable($pageUid);

        if (empty($resolvedPageUid)) {
            $resolvedPageUid = $pageUid;
        }

        return (int)$resolvedPageUid;
    }

    /**
     * @param int $pageUid
     * @return string
     */
    protected function getUrl($pageUid)
    {
        $arguments = [];

        $values = $this->templateVariableContainer->getAll();
        if ($this->getTemplateService()->hasPersistingTable() && !empty($values['token'])) {
            $arguments['token'] = $values['token'];
        }

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        return $uriBuilder
            ->setTargetPageUid($pageUid)
            ->setCreateAbsoluteUri(true)
            ->setArguments($arguments)
            ->build();
    }

    /**
     * @param int $templateIdentifier
     * @return TemplateService
     */
    protected function getTemplateService()
    {

        $values = $this->templateVariableContainer->getAll();
        return GeneralUtility::makeInstance(TemplateService::class, $values['templateIdentifier']);
    }

}
