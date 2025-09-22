<?php

declare(strict_types=1);

namespace Fab\Formule\Processor;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Processor\AbstractProcessor;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FileUploadProcessor
 */
class PdfUploadProcessor extends AbstractProcessor
{

    /**
     * @param array $values
     * @param string $insertOrUpdate
     * @return array
     */
    public function process(array $values, string $insertOrUpdate = ''): array
    {

        $savedFieldName = $this->savePdf();
        if (strlen($savedFieldName) > 0) {
            $values['file'] = $savedFieldName;
        } else {
            unset($values['file']);
        }

        return $values;
    }

    /**
     * Save the uploaded PDF if valid or delete it if marked to be deleted
     *
     * @return string
     */
    private function savePdf(): string
    {
        $fileName = '';
        $storage = GeneralUtility::makeInstance(ResourceFactory::class)->getStorageObject('uid');

        if (isset($_FILES['file'])) {

            $uploadedFile = $_FILES['file'];
            $fileSize = (int)$uploadedFile['size'];

            // Only save if we successfully uploaded something
            if ($uploadedFile['error'] === UPLOAD_ERR_OK && $fileSize > 0 && $fileSize <= GeneralUtility::getMaxUploadFileSize()) {

                // Cancel if not a PDF
                $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
                $mime = finfo_file($finfo, $uploadedFile['tmp_name']);
                finfo_close($finfo);
                if ($mime === 'application/pdf') {
                    $file = $storage->addUploadedFile($uploadedFile);
                    $fileName = $file->getName();
                }

            }
        }

        return $fileName;
    }


    /**
     * Returns an instance of the current Frontend User.
     *
     * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected function getFrontendUser(): \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
    {
        return $GLOBALS['TSFE']->fe_user;
    }

}
