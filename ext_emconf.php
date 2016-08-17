<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Variety of forms - effortless!',
    'description' => 'Render a variety of forms template based on the FE such as contact form, registration form, etc... effortless!',
    'category' => 'plugin',
    'author' => 'Udriot Fabien',
    'author_email' => 'fabien.udriot@ecodev.ch',
    'state' => 'beta',
    'version' => '1.0.0-dev',
    'psr-4' => [
        'Fab\\Formule\\' => 'Classes'
    ],
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-7.99.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
            'vidi' => ''
        ],
    ],
];