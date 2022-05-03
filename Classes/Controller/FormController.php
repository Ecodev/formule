<?php
namespace Fab\Formule\Controller;

/*
 * This file is part of the Fab/Formule project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Formule\Service\ArgumentService;
use Fab\Formule\Service\DataService;
use Fab\Formule\Service\FlashMessageQueue;
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
use TYPO3\CMS\Extbase\Annotation as Extbase;

/**
 * FormController
 */
class FormController extends ActionController
{

    /**
     * @return string|null
     */
    public function showAction(): ?string
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
                $values = $loader->load($values);
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

    public function initializeSubmitAction(): void
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
     * @Extbase\Validate("\Fab\Formule\Domain\Validator\HoneyPotValidator", param="values")
     * @Extbase\Validate("\Fab\Formule\Domain\Validator\ValuesValidator", param="values")
     * @Extbase\Validate("\Fab\Formule\Domain\Validator\UserDefinedValidator", param="values")
     */
    public function submitAction(array $values = []): void
    {
        // Fix settings in case two instances are loaded on the same page
        $this->settings = array_merge($this->settings, ArgumentService::getSettings());

        if ($this->request->getMethod() !== 'POST') {
            throw new \RuntimeException('Form must be submitted using POST');
        }

        $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'beforeProcessValues', [$values]);
        $values = $signalResult[0];

        // Check the template path according to the Plugin settings.
        $templateService = $this->getTemplateService();

        if ($templateService->hasPersistingTable()) {
            if ($this->getDataService()->recordExists()) {
                $values = $this->getDataService()->update($values);
                $label = 'LLL:EXT:formule/Resources/Private/Language/locallang.xlf:message.update.success';
                $this->getFormuleFlashMessageQueue()->success($label);
            } else {
                $values = $this->getDataService()->create($values);
                $label = 'LLL:EXT:formule/Resources/Private/Language/locallang.xlf:message.create.success';
                $this->getFormuleFlashMessageQueue()->success($label);
            }

            $signalResult = $this->getSignalSlotDispatcher()->dispatch(self::class, 'afterPersistValues', [$values]);
            $values = $signalResult[0];
        }

        // We want this information in the values array.
        $values['templateIdentifier'] = $this->settings['template'];
        $values['HTTP_HOST'] = GeneralUtility::getIndpEnv('HTTP_HOST');
        $values['HTTP_REFERER'] = GeneralUtility::getIndpEnv('HTTP_REFERER');

        // Possible finishers
        foreach ($templateService->getFinishers() as $className) {

            /** @var \Fab\Formule\Finisher\FinisherInterface $finisher */
            $finisher = GeneralUtility::makeInstance($className);
            $values = $finisher->finish($values);
        };

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
            $url = $templateService->getRedirectUrl($values);
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
    public function feedbackAction(): string
    {
        // We can retrieve only once.
        $values = $this->getRegistryService()->get('values');

        // Will be null if the User reload the feedback action
        if ($values === null) {
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

    protected function getSignalSlotDispatcher(): Dispatcher
    {
        return $this->objectManager->get(Dispatcher::class);
    }

    protected function getTemplateService(): TemplateService
    {
        return GeneralUtility::makeInstance(TemplateService::class, $this->settings['template']);
    }

    protected function getArgumentService(): ArgumentService
    {
        return GeneralUtility::makeInstance(ArgumentService::class);
    }

    protected function getRegistryService(): RegistryService
    {
        return GeneralUtility::makeInstance(RegistryService::class);
    }

    protected function getMessageService($messageType): MessageService
    {
        return GeneralUtility::makeInstance(MessageService::class, $this->settings, $messageType);
    }

    protected function getDataService(): DataService
    {
        return GeneralUtility::makeInstance(DataService::class, $this->settings['template']);
    }

    protected function getFormuleFlashMessageQueue(): FlashMessageQueue
    {
        return GeneralUtility::makeInstance(FlashMessageQueue::class);
    }

}
