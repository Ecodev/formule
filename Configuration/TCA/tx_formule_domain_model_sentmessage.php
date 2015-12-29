<?php
return array(
    'ctrl' => [
        'title' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:tx_formule_domain_model_sentmessage',
        'label' => 'sender',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',

        'delete' => 'deleted',
        'searchFields' => 'sender,recipient,subject,body,attachment,context,was_opened,sent_time,ip,',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('formule') . 'Resources/Public/Icons/tx_formule_domain_model_sentmessage.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, sender, recipient, subject, body, attachment, context, was_opened, sent_time, ip',
    ],
    'types' => [
        '1' => ['showitem' => 'hidden;;1, sender, recipient, subject, body, attachment, context, was_opened, sent_time, ip, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [

        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],

        'sender' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:sender',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'recipient' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:recipient',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'subject' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:subject',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'body' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:body',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'attachment' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:attachment',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'context' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:context',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'was_opened' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:was_opened',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'sent_time' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:sent_time',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'ip' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:ip',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

    ],
);