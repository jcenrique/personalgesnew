<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Autentifikazioa aplikazioa',

            'below_content' => 'Denbora osoa aplikazio seguru bat erabili saioa hastea egiaztatzeko kode bat sortzeko.',

            'messages' => [
                'enabled' => 'Aktibaturik',
                'disabled' => 'Desaktibaturik',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Erabili zure autentifikazioa aplikazioaren kodea',

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

];
