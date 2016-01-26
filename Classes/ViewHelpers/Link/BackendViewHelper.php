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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper to create a link to the BE.
 */
class BackendViewHelper extends AbstractViewHelper
{

    /**
     * @return string
     */
    public function render()
    {
        $content = $this->renderChildren();

        if ($content) {
            $link = sprintf('<a href="%s">%s</a>', $this->getUrl(), $content);
        } else {
            $link = $this->getUrl();
        }

        return $link;
    }

    /**
     * @return string
     */
    protected function getUrl()
    {
        $values = $this->templateVariableContainer->getAll();
        $parsedURL = parse_url($values['HTTP_REFERER']);
        $url = $parsedURL['scheme'] . '://' . $parsedURL['host'] . '/typo3';
        return $url;
    }

}
