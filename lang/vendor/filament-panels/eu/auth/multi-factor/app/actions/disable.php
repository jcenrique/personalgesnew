<?php

return [

    'label' => 'Itzali',

    'modal' => [

        'heading' => 'Autentifikazioa aplikazioa desgaitu',

        'description' => 'Ziur al zaude autentifikazioaren aplikazioa erabiltzea utzitu nahi duzula? Desgaitzeak zure kontua segurtasunerako geruza bat kendu egingo du.',

        'form' => [

            'code' => [

                'label' => 'Autentifikazioaren aplikazioko 6 digituko kodea sartu',

                'validation_attribute' => 'kodea',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Berreskurapen kodea erabili ordez',
                    ],

                ],

                'messages' => [

                    'invalid' => 'Sarturiko kodea ez da baliogarria.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Edo berreskurapen kodea sartu',

                'validation_attribute' => 'berreskurapen kodea',

                'messages' => [

                    'invalid' => 'Sarturiko berreskurapen kodea ez da baliogarria.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Autentifikazioa aplikazioa desgaitu',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Autentifikazioa aplikazioa desaktibaturik',
        ],

    ],

];
