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
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper to create a confirm link.
 */
class ConfirmViewHelper extends AbstractViewHelper
{

    /**
     * @param string|int $pageUid
     * @return string
     */
    public function render($pageUid)
    {

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

        return $resolvedPageUid;
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

        $url = $this->getUriBuilder()
            ->setTargetPageUid($pageUid)
            ->setUseCacheHash(false)
            ->setCreateAbsoluteUri(true)
            ->setArguments($arguments)
            ->build();

        return $url;
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

    /**
     * @return UriBuilder
     */
    protected function getUriBuilder()
    {
        /** @var $uriBuilder UriBuilder */
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        return $uriBuilder;
    }

}
