<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Variety of forms - effortless!',
    'description' => 'Render a variety of forms template based on the FE such as contact form, registration form, etc... effortless!',
    'category' => 'plugin',
    'author' => 'Udriot Fabien',
    'author_email' => 'fabien@ecodev.ch',
    'state' => 'stable',
    'version' => '2.4.0',
    'psr-4' => [
        'Fab\\Formule\\' => 'Classes'
    ],
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
            'vidi' => '4.0.0-0.0.0'
        ],
    ],
];
