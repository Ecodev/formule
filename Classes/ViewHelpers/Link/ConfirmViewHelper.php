<?php
namespace Fab\Formule\ViewHelpers\Link;

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
     * @param int $pageUid
     * @return string
     */
    public function render($pageUid)
    {

        $content = $this->renderChildren();

        if ($content) {
            $link = sprintf('<a href="%s">%s</a>', $this->getUrl($pageUid), $content);
        } else {
            $link = $this->getUrl($pageUid);
        }

        return $link;

    }

    /**
     * @param int $pageUid
     * @return string
     */
    protected function getUrl($pageUid)
    {
        $arguments = [];
        $values = $this->templateVariableContainer->getAll();
        $templateService = $this->getTemplateService($values['templateIdentifier']);

        if ($templateService->hasPersistingTable() && !empty($values['token'])) {
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
    protected function getTemplateService($templateIdentifier)
    {
        return GeneralUtility::makeInstance(TemplateService::class, $templateIdentifier);
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
