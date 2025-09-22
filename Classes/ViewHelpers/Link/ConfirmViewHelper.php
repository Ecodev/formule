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

    public function __construct(private \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder)
    {
    }
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
    public function render(): string
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
    protected function resolvePageUid($pageUid): int
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
    protected function getUrl(int $pageUid): string
    {
        $arguments = [];

        $values = $this->templateVariableContainer->getAll();
        if ($this->getTemplateService()->hasPersistingTable() && !empty($values['token'])) {
            $arguments['token'] = $values['token'];
        }

        $uriBuilder = $this->uriBuilder;
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
    protected function getTemplateService(): TemplateService
    {

        $values = $this->templateVariableContainer->getAll();
        return GeneralUtility::makeInstance(TemplateService::class, (int)$values['templateIdentifier']);
    }

}
