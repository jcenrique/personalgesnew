<?php

return [

    'label' => 'Profila',

    'form' => [

        'email' => [
            'label' => 'Posta helbidea',
        ],

        'name' => [
            'label' => 'Izena',
        ],

        'password' => [
            'label' => 'Pasahitz berria',
            'validation_attribute' => 'pasahitza',
        ],

        'password_confirmation' => [
            'label' => 'Pasahitz berrirn berretsi',
            'validation_attribute' => 'pasahitzaren berretsia',
        ],

        'current_password' => [
            'label' => 'Uneko pasahitza',
            'below_content' => 'Segurtasunagatik, mesedez berretsi zure pasahitza jarraitzeko.',
            'validation_attribute' => 'uneko pasahitza',
        ],

        'actions' => [

            'save' => [
                'label' => 'Aldaketak gorde',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Bi faktoreen autentifikazioa (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Posta aldaketa eskaera bidalita',
            'body' => 'Zure posta helbidea :email-ra aldatzeko eskaera bidali dugu. Mesedez, begiratu zure postakoa aldaketa berretsi dezakezu.',
        ],

        'saved' => [
            'title' => 'Aldaketak gordeta',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Itzuli',
        ],

    ],

];
