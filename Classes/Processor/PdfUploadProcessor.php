<?php
namespace Ecodev\Speciality\Processor;

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

use Fab\Formule\Processor\AbstractProcessor;
use Fab\Formule\Processor\ProcessorInterface;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Saltedpasswords\Salt\SaltFactory;
use TYPO3\CMS\Saltedpasswords\Utility\SaltedPasswordsUtility;

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
    public function process(array $values, $insertOrUpdate = '')
    {

        $savedFieldName = $this->savePdf('file');
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
     * @param integer $fieldName
     * @return string
     */
    private function savePdf($fieldName)
    {

        $fileName = '';
        $storage = ResourceFactory::getInstance()->getStorageObject('uid');

        if (isset($_FILES[$fieldName])) {

            $uploadedFile = $_FILES[$fieldName];
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
    protected function getFrontendUser()
    {
        return $GLOBALS['TSFE']->fe_user;
    }

}
