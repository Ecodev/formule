<?php
namespace Fab\Formule\Controller;

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

use Fab\Formule\Domain\Repository\DataRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * FormController
 */
class FormController extends ActionController
{

    /**
     * @var \Fab\Formule\Domain\Repository\DataRepository
     * @inject
     */
    protected $dataRepository;

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeAction()
    {

//        if ($this->arguments->hasArgument('data')) {
//
//            /** @var \Fab\VidiFrontend\TypeConverter\ContentConverter $typeConverter */
//            $typeConverter = $this->objectManager->get('Fab\VidiFrontend\TypeConverter\DataConverter');
//
//            $this->arguments->getArgument('content')
//                ->getPropertyMappingConfiguration()
//                ->setTypeConverter($typeConverter);
//        }

    }

    /**
     * @return void
     */
    public function showAction()
    {
        if (empty($this->settings['template'])) {
            return '<strong style="color: red">Please select a "formule" template!</strong>';
        }

        // Configure the template path according to the Plugin settings.
        $pathAbs = GeneralUtility::getFileAbsFileName($this->settings['template']);
        if (!is_file($pathAbs)) {
            return sprintf('<strong style="color:red;">I could not find the template file %s.</strong>', $pathAbs);
        }

        $this->view->setTemplatePathAndFilename($pathAbs);

        // Send signal
//        $dataType = $this->settings['dataType'];
    }

    /**
     * @param array $data
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function submitAction(array $data = [])
    {
        $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'processMarkers', [$data]);
        $data = $signalResult[0];

        if (empty($data[DataRepository::IDENTIFIER])) {
            $this->dataRepository->create($data);
        } else {
            $this->dataRepository->update($data);
        }

        if ($this->settings['sendEmailToUser']) {
            $this->messageService()->send($this->settings, $markers);
            $this->getLogginService()->log($this->settings, $markers);
        }

        if ($this->settings['sendEmailToAdmin']) {
            $this->messageService()->send($this->settings, $markers);
            $this->getLogginService()->log($this->settings, $markers);
        }

        $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'postDataPersist', [$data]);
        $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'beforeRedirect', [$data]);
    }

    /**
     * Get the SignalSlot dispatcher.
     *
     * @return Dispatcher
     */
    protected function getSignalSlotDispatcher() {
        return $this->objectManager->get(Dispatcher::class);
    }

}