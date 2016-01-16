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

use Fab\Formule\Service\ArgumentService;
use Fab\Formule\Service\DataService;
use Fab\Formule\Service\TemplateService;
use Fab\Formule\TypeConverter\ValuesConverter;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * FormController
 */
class FormController extends ActionController
{

    /**
     * @var \Fab\Formule\Service\DataService
     * @inject
     */
    protected $dataService;

    /**
     * @return string|null
     */
    public function showAction()
    {
        $message = null;
        if (empty($this->settings['template'])) {
            $message = '<strong style="color: red">Please select a template in formule!</strong>';
        } else {

            // Check the template path according to the Plugin settings.
            $templateService = $this->getTemplateService($this->settings['template']);
            $pathAbs = $templateService->getResolvedPath();
            if (!is_file($pathAbs)) {
                return sprintf('<strong style="color:red;">I could not find the template file %s.</strong>', $pathAbs);
            }

            $this->view->setTemplatePathAndFilename($pathAbs);
            $this->view->assign('contentElement', $this->configurationManager->getContentObject()->data);
            $this->view->assign('values', $this->getArgumentService()->getValues());
        }

        return $message;
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeSubmitAction()
    {
        /** @var ValuesConverter $typeConverter */
        $typeConverter = $this->objectManager->get(ValuesConverter::class);

        if ($this->arguments->hasArgument('values')) {
            $this->arguments->getArgument('values')
                ->getPropertyMappingConfiguration()
                ->setTypeConverter($typeConverter);
        }
    }

    /**
     * @param array $values
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @validate $values \Fab\Formule\Validator\HoneyPotValidator
     * @validate $values \Fab\Formule\Validator\FieldValuesValidator
     *
     */
    public function submitAction(array $values = [])
    {

        var_dump(123);
        exit();

        $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'processValues', [$values]);
        $values = $signalResult[0];

        if (empty($values[DataService::IDENTIFIER])) {
            $this->dataService->create($values);
        } else {
            $this->dataService->update($values);
        }

        if ($this->settings['sendEmailToUser']) {
            $this->messageService()->send($this->settings, $markers);
            $this->getLogginService()->log($this->settings, $markers);
        }

        if ($this->settings['sendEmailToAdmin']) {
            $this->messageService()->send($this->settings, $markers);
            $this->getLogginService()->log($this->settings, $markers);
        }

        $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'postDataPersist', [$values]);
        $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'beforeRedirect', [$values]);
    }

    /**
     * Get the SignalSlot dispatcher.
     *
     * @return Dispatcher
     */
    protected function getSignalSlotDispatcher() {
        return $this->objectManager->get(Dispatcher::class);
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
     * @return ArgumentService
     */
    protected function getArgumentService()
    {
        return GeneralUtility::makeInstance(ArgumentService::class);
    }

}