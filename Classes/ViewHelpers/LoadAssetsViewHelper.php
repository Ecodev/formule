<?php
namespace Fab\Formule\ViewHelpers;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Service\TemplateService;
use FluidTYPO3\Vhs\Asset;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper to load a JavaScript file
 */
class LoadAssetsViewHelper extends AbstractViewHelper
{
    const TYPE_JS = 'js';
    const TYPE_CSS = 'css';

    /**
     * @return void
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('footer', 'bool', '', false, true);
        $this->registerArgument('type', 'string', '', false, self::TYPE_JS);
    }

    public function render(): void
    {
        $footer = $this->arguments['footer'];
        $type = $this->arguments['type'];
        // Get variables
        $settings = $this->templateVariableContainer->get('settings');

        // Inline code
        $rawInlineCode = $this->renderChildren();
        $inlineCode = $this->sanitizeInlineCode($rawInlineCode ?? '');

        $name = $this->computeName($inlineCode);
        if ($this->hasInlineCode($inlineCode)) {
            if ($this->shouldLoadByVhs($settings)) {
                $this->loadByVhsInline($name, $inlineCode, $footer, $type);
            } else {
                $this->loadByCorePageRenderInline($name, $inlineCode, $footer, $type);
            }
        } else {

            $templateService = $this->getTemplateService($settings['template']);
            foreach ($templateService->getAssets() as $asset) {
                if ($this->shouldLoadByVhs($settings)) {
                    $asset['name'] = md5($asset['path']);
                    $this->loadByVhs($asset);
                } else {
                    $this->loadByCorePageRender($asset);
                }
            }
        }
    }

    /**
     * @param string $inlineCode
     * @return string
     */
    protected function computeName(string $inlineCode): string
    {
        $contentElement = $this->templateVariableContainer->get('contentElement');
        $inlineCodeExtract = substr(trim($inlineCode), 0, 100);
        return md5($contentElement['uid'] . $inlineCodeExtract);
    }

    /**
     * @param string $inlineCode
     * @return bool
     */
    protected function hasInlineCode(string $inlineCode): bool
    {
        return !empty(trim($inlineCode));
    }

    /**
     * @param string $inlineCode
     * @return string
     */
    protected function sanitizeInlineCode(string $inlineCode): string
    {
        if (!empty($inlineCode)) {
            $inlineCode = preg_replace('#<script(.*?)>(.*)</script>#is', '$2', $inlineCode);
        }
        return $inlineCode;
    }

    /**
     * @param string $name
     * @param string $content
     * @param bool $footer
     * @param string $type
     */
    protected function loadByVhsInline(string $name, string $content, bool $footer = true, $type = self::TYPE_JS)
    {
        $configuration = [
            'content' => $content,
            // Asset::createFromSettings expects 'dependencies' to be an array.
            // Previously this was a string which raised InvalidTypeException.
            'dependencies' => ['mainJs'], # could be configurable.
            #'standalone' => false,
            #'rewrite' => false,
            'movable' => $footer,
            'type' => $type,
            'name' => $name,
        ];
        Asset::createFromSettings($configuration);
    }

    /**
     * @param array $asset
     * @return void
     */
    protected function loadByVhs(array $asset)
    {
        if (Environment::getContext()->isDevelopment()) {
            $developmentFile = $this->getDevelopmentFile($asset);
            if ($developmentFile) {
                $asset['path'] = str_replace('.min.', '.', $asset['path']);
            }
        }
        // Ensure 'dependencies' is an array to satisfy Asset::createFromSettings expectations.
        if (isset($asset['dependencies']) && !is_array($asset['dependencies'])) {
            $asset['dependencies'] = [$asset['dependencies']];
        }
        Asset::createFromSettings($asset);
    }

    /**
     * @param string $name
     * @param string $content
     * @param bool $footer
     * @param string $type
     */
    protected function loadByCorePageRenderInline(string $name, string $content, bool $footer = true, string $type = self::TYPE_JS)
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        if ($type === self::TYPE_JS) {
            if ($footer) {
                $pageRenderer->addJsFooterInlineCode($name, $content);
            } else {
                $pageRenderer->addJsInlineCode($name, $content);
            }
        } elseif ($content['type'] === 'css') {
            $pageRenderer->addCssInlineBlock($name, $content);
        }
    }

    /**
     * @param array $asset
     * @return void
     */
    protected function loadByCorePageRender(array $asset)
    {

        $file = $this->resolveFileForApplicationContext($asset);

        $fileNameAndPath = GeneralUtility::getFileAbsFileName($file);
        $fileNameAndPath = PathUtility::stripPathSitePrefix($fileNameAndPath);

        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        if ($asset['type'] === 'js') {
            $pageRenderer->addJsFooterFile($fileNameAndPath);
        } elseif ($asset['type'] === 'css') {
            $pageRenderer->addCssFile($fileNameAndPath);
        }
    }

    /**
     * @param array $settings
     * @return bool
     */
    protected function shouldLoadByVhs(array $settings)
    {
        return ExtensionManagementUtility::isLoaded('vhs') && $settings['loadAssetWithVhsIfAvailable'];
    }

    /**
     * @param array $asset
     * @return string|NULL
     */
    protected function getDevelopmentFile(array $asset): ?string
    {
        $possibleDevelopmentFile = str_replace('.min.', '.', $asset['path']);
        $developmentFile = GeneralUtility::getFileAbsFileName($possibleDevelopmentFile);
        if (!file_exists($developmentFile)) {
            $developmentFile = NULL;
        }
        return $developmentFile;
    }

    /**
     * @param array $asset
     * @return string
     */
    protected function resolveFileForApplicationContext(array $asset): string
    {
        $resolvedFile = $asset['path']; // default value

        // check if there is a non minimized file for context Development
        if (Environment::getContext()->isDevelopment()) {
            $developmentFile = $this->getDevelopmentFile($asset);
            if ($developmentFile) {
                $resolvedFile = $developmentFile;
            }
        }
        return $resolvedFile;
    }


    /**
     * Returns an instance of the Frontend object.
     *
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getFrontendObject(): \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * @param string $templateIdentifier
     * @return TemplateService
     */
    protected function getTemplateService(string $templateIdentifier): TemplateService
    {
        return GeneralUtility::makeInstance(TemplateService::class, (int)$templateIdentifier);
    }


}
