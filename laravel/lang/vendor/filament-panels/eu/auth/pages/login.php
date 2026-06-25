<?php

return [

    'title' => 'Sarrera',

    'heading' => 'Sartu zure kontuan',

    'actions' => [

        'register' => [
            'before' => 'edo',
            'label' => 'Ireki kontua',
        ],

        'request_password_reset' => [
            'label' => '¿Pasahitza ahaztu duzu?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Posta elektronikoa',
        ],

        'password' => [
            'label' => 'Pasahitza',
        ],

        'remember' => [
            'label' => 'Gogoratzen bazaitu',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Sartu',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Egiaztatu zure identitatea',

        'subheading' => 'Saioa hastea jarraitzeko, zure identitatea egiaztatu beharko duzu.',

        'form' => [

            'provider' => [
                'label' => 'Nola nahi duzu egiaztatzea?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Saioa hastea berretsi',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Kredentzioak ez datoz bat gure erregistroekin.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Saiakera gehiegi. Saiatu berriro :seconds segundotan.',
            'body' => 'Saiatu berriro :seconds segundotan.',
        ],

    ],

];
