<?php

return [

    'title' => 'Zure pasahitza berreskuratu',

    'heading' => 'Zure pasahitza berreskuratu',

    'form' => [

        'email' => [
            'label' => 'Pasahitza berreskuratu',
        ],

        'password' => [
            'label' => 'Pasahitza',
            'validation_attribute' => 'pasahitza',
        ],

        'password_confirmation' => [
            'label' => 'Pasahitza berretsi',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Pasahitza berreskuratu',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Pasahitza berreskuratzeko saiakera gehiegi',
            'body' => 'Mesedez, saiatu berriro :seconds segundotan.',
        ],

    ],

];
