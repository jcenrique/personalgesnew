<?php

return [

    'title' => 'Izena eman',

    'heading' => 'Kontua sortu',

    'actions' => [

        'login' => [
            'before' => 'edo',
            'label' => 'saioa hasi zure kontuan',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Posta elektronikoa',
        ],

        'name' => [
            'label' => 'Izena',
        ],

        'password' => [
            'label' => 'Pasahitza',
            'validation_attribute' => 'pasahitza',
        ],

        'password_confirmation' => [
            'label' => 'Pasahitza berretsi',
        ],

        'actions' => [

            'register' => [
                'label' => 'Izena eman',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Izena ematetako saiakera gehiegi',
            'body' => 'Mesedez, saiatu berriro :seconds segundotan.',
        ],

    ],

];
