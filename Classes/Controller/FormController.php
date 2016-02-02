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
use Fab\Formule\Service\FlushMessageQueue;
use Fab\Formule\Service\MessageService;
use Fab\Formule\Service\RegistryService;
use Fab\Formule\Service\TemplateService;
use Fab\Formule\TypeConverter\ValuesConverter;
use Michelf\Markdown;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * FormController
 */
class FormController extends ActionController
{

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
            $templateService = $this->getTemplateService();

            $values = $this->getArgumentService()->getValues();

            // Possible loaders
            foreach ($templateService->getLoaders() as $className) {

                /** @var \Fab\Formule\Loader\LoaderInterface $loader */
                $loader = GeneralUtility::makeInstance($className);
                $values = $loader->intercept($values);
            };

            // Set final template path.
            $pathAbs = $templateService->getResolvedPath();
            if (!is_file($pathAbs)) {
                return sprintf('<strong style="color:red;">I could not find the template file %s.</strong>', $pathAbs);
            }

            $this->view->setTemplatePathAndFilename($pathAbs);
            $this->view->assign('contentElement', $this->configurationManager->getContentObject()->data);
            $this->view->assign('values', $values);
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
     * @validate $values \Fab\Formule\Domain\Validator\HoneyPotValidator
     * @validate $values \Fab\Formule\Domain\Validator\ValuesValidator
     * @validate $values \Fab\Formule\Domain\Validator\UserDefinedValidator
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function submitAction(array $values = [])
    {

        $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'beforeProcessValues', [$values]);
        $values = $signalResult[0];

        // Check the template path according to the Plugin settings.
        $templateService = $this->getTemplateService();

        if ($templateService->hasPersistingTable()) {
            if ($this->getDataService()->recordExists()) {
                $values = $this->getDataService()->update($values);
                $label = 'LLL:EXT:formule/Resources/Private/Language/locallang.xlf:message.update.success';
                $this->getFlushMessageQueue()->success($label);
            } else {
                $values = $this->getDataService()->create($values);
                $label = 'LLL:EXT:formule/Resources/Private/Language/locallang.xlf:message.create.success';
                $this->getFlushMessageQueue()->success($label);
            }

            $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'afterPersistValues', [$values]);
            $values = $signalResult[0];
        }

        // We want this information in the values array.
        $values['templateIdentifier'] = $this->settings['template'];
        $values['HTTP_HOST'] = GeneralUtility::getIndpEnv('HTTP_HOST');
        $values['HTTP_REFERER'] = GeneralUtility::getIndpEnv('HTTP_REFERER');

        // Possible email to admin.
        if (!empty($this->settings['emailAdminTo'])) {
            $this->getMessageService(MessageService::TO_ADMIN)->send($values);
        }

        // Possible email to user.
        if (!empty($this->settings['emailUserTo'])) {
            $this->getMessageService(MessageService::TO_USER)->send($values);
        }

        $this->getSignalSlotDispatcher()->dispatch(self::class, 'beforeRedirect', [$values]);

        // Save in registry... Trick to avoid POSTing the arguments again which might contain very long text.
        $this->getRegistryService()->set('values', $values);

        if ($templateService->hasRedirect() && !$templateService->isDefaultRedirectAction()) {
            $url = $templateService->getRedirectUrl();
            HttpUtility::redirect($url);
        } else {
            $this->redirect(
                $templateService->getRedirectAction(),
                $templateService->getRedirectController(),
                null,
                [],
                $templateService->getRedirectPageUid()
            );
        }
    }

    /**
     * @return string
     */
    public function feedbackAction()
    {
        // We can retrieve only once.
        $values = $this->getRegistryService()->get('values');

        // Will be null if the User reload the feedback action
        if (is_null($values)) {
            $this->redirect('show');
        }

        /** @var StandaloneView $view */
        $view = $this->objectManager->get(StandaloneView::class);
        $view->assignMultiple($values);

        $templateService = $this->getTemplateService();
        $body = $templateService->getSection(TemplateService::SECTION_FEEDBACK);

        if (empty($body)) {
            $view->setTemplateSource($this->settings['feedbackMessage']);
            $content = trim($view->render());
            $feedback = Markdown::defaultTransform($content);
        } else {
            // Check the template path according to the Plugin settings.
            $view->setTemplateSource($body);
            $feedback = trim($view->render());
        }

        return $feedback;
    }

    /**
     * Get the SignalSlot dispatcher.
     *
     * @return Dispatcher
     */
    protected function getSignalSlotDispatcher()
    {
        return $this->objectManager->get(Dispatcher::class);
    }

    /**
     * @return TemplateService
     */
    protected function getTemplateService()
    {
        return GeneralUtility::makeInstance(TemplateService::class, $this->settings['template']);
    }

    /**
     * @return ArgumentService
     */
    protected function getArgumentService()
    {
        return GeneralUtility::makeInstance(ArgumentService::class);
    }

    /**
     * @return RegistryService
     */
    protected function getRegistryService()
    {
        return GeneralUtility::makeInstance(RegistryService::class);
    }

    /**
     * @param string $messageType
     * @return MessageService
     */
    protected function getMessageService($messageType)
    {
        return GeneralUtility::makeInstance(MessageService::class, $this->settings, $messageType);
    }

    /**
     * @return DataService
     */
    protected function getDataService()
    {
        return GeneralUtility::makeInstance(DataService::class, $this->settings['template']);
    }

    /**
     * @return FlushMessageQueue
     */
    protected function getFlushMessageQueue()
    {
        return GeneralUtility::makeInstance(FlushMessageQueue::class);
    }

}