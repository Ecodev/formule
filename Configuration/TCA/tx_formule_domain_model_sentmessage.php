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
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('formule') . 'Resources/Public/Images/tx_formule_domain_model_sentmessage.png'
    ],
    'interface' => [
        'showRecordFieldList' => 'sender, recipient, subject, body, attachment, context, was_opened, sent_time, ip',
    ],
    'types' => [
        '1' => ['showitem' => 'sender, recipient, subject, body, attachment, context, was_opened, sent_time, ip'],
    ],
    'columns' => [

        'sender' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:sender',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
                'eval' => 'trim'
            ],
        ],
        'recipient' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:recipient',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
                'eval' => 'trim'
            ],
        ],
        'subject' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:subject',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
                'eval' => 'trim'
            ],
        ],
        'body' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:body',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
                'eval' => 'trim'
            ],
        ],
        'attachment' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:attachment',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
                'eval' => 'trim'
            ],
        ],
        'context' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:context',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
                'eval' => 'trim'
            ],
        ],
        'was_opened' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:was_opened',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
                'eval' => 'trim'
            ],
        ],
        'sent_time' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:sent_time',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
                'eval' => 'trim'
            ],
        ],
        'ip' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:formule/Resources/Private/Language/tx_formule_domain_model_sentmessage.xlf:ip',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'readOnly' => true,
                'eval' => 'trim'
            ],
        ],

    ],
);