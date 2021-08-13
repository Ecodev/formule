<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Variety of forms - effortless!',
    'description' => 'Render a variety of forms template based on the FE such as contact form, registration form, etc... effortless!',
    'category' => 'plugin',
    'author' => 'Udriot Fabien',
    'author_email' => 'fabien@ecodev.ch',
    'state' => 'stable',
    'version' => '3.0.0',
    'psr-4' => [
        'Fab\\Formule\\' => 'Classes'
    ],
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
            'vidi' => '5.0.0-0.0.0'
        ],
    ],
];
