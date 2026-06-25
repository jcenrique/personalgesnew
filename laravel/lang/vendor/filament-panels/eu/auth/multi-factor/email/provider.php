<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Posta elektronikoko egiaztapen kodeak',

            'below_content' => 'Kode denboral bat jaso zure postakoan zure identitatea saioa hastean egiaztatzeko.',

            'messages' => [
                'enabled' => 'Aktibaturik',
                'disabled' => 'Desaktibaturik',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Kodea bidaltzea zure postakora',

        'code' => [

            'label' => 'Postako bidali digun 6 digituko kodea sartu',

            'validation_attribute' => 'kodea',

            'actions' => [

                'resend' => [

                    'label' => 'Kode berri bat postaz bidaltzea',

                    'notifications' => [

                        'resent' => [
                            'title' => 'Kode berri bat postaz bidali dugu.',
                        ],

                        'throttled' => [
                            'title' => 'Bidalketako saiakera gehiegi. Itxoin kode berri bat eskatu aurretik.',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'Sarturiko kodea ez da baliogarria.',

            ],

        ],

    ],

];
