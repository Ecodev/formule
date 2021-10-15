<?php
defined('TYPO3_MODE') or die();

call_user_func(
    function () {

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1634289580] = [
            'nodeName' => 'formuleRenderNameFrom',
            'priority' => 40,
            'class' => \Fab\Formule\Backend\FormuleRenderNameFromElement::class,
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1634289581] = [
            'nodeName' => 'formuleRenderEmailFrom',
            'priority' => 40,
            'class' => \Fab\Formule\Backend\FormuleRenderEmailFromElement::class,
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1634289582] = [
            'nodeName' => 'formuleRenderFeedback',
            'priority' => 40,
            'class' => \Fab\Formule\Backend\FormuleRenderFeedbackElement::class,
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1634289583] = [
            'nodeName' => 'formuleRenderEmailUserBody',
            'priority' => 40,
            'class' => \Fab\Formule\Backend\FormuleRenderEmailUserBodyElement::class,
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1634289584] = [
            'nodeName' => 'formuleRenderEmailAdminBody',
            'priority' => 40,
            'class' => \Fab\Formule\Backend\FormuleRenderEmailAdminBodyElement::class,
        ];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1634289585] = [
            'nodeName' => 'formuleRenderSummary',
            'priority' => 40,
            'class' => \Fab\Formule\Backend\FormuleRenderSummaryElement::class,
        ];

        $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
        )->get('formule');

        if (FALSE === isset($configuration['autoload_typoscript']) || TRUE === (bool)$configuration['autoload_typoscript']) {

            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
                'formule',
                'constants',
                '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:formule/Configuration/TypoScript/constants.typoscript">'
            );

            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
                'formule',
                'setup',
                '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:formule/Configuration/TypoScript/setup.typoscript">'
            );
        }

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Fab.formule',
            'Pi1',
            array(
                'Form' => 'show, submit, feedback',

            ),
            // non-cacheable actions
            array(
                'Form' => 'show, submit, feedback',

            )
        );

        // Duplicate feature of EXT:messenger
        if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('messenger')) {

            // Override classes for the Object Manager
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\CMS\Core\Mail\MailMessage'] = array(
                'className' => 'Fab\Formule\Override\Core\Mail\MailMessage'
            );

            # Install PSR-0-compatible class autoloader for Markdown Library in Resources/PHP/Michelf
            spl_autoload_register(function ($class) {
                if (strpos($class, 'Michelf\Markdown') !== FALSE) {
                    require sprintf('%sResources/Private/PHP/Markdown/%s',
                        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('formule'),
                        preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, ltrim($class, '\\')) . '.php'
                    );
                }
            });
        }


        /** @var $signalSlotDispatcher \TYPO3\CMS\Extbase\SignalSlot\Dispatcher */
        $signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);

        // Connect some signals with slots.
        $signalSlotDispatcher->connect(
            \Fab\Formule\Controller\FormController::class,
            'beforeProcessValues',
            \Fab\Formule\Slot\ValuesSanitizer::class,
            'sanitize',
            true
        );

        // Register icons
        $icons = [
            'content-formule' => 'EXT:formule/Resources/Public/Images/Formule.png',
        ];

        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        foreach ($icons as $identifier => $path) {
            $iconRegistry->registerIcon(
                $identifier, TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class, ['source' => $path]
            );
        }

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            'mod {
                wizards.newContentElement.wizardItems.plugins {
                    elements {
                        formule_pi1 {
                            iconIdentifier = content-formule
                            title = LLL:EXT:formule/Resources/Private/Language/locallang.xlf:wizard.title
                            description = LLL:EXT:formule/Resources/Private/Language/locallang.xlf:wizard.description
                            tt_content_defValues {
                                CType = list
                                list_type = formule_pi1
                            }
                        }
                    }
                }
            }'
        );
    }
);
