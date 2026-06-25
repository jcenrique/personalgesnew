<?php

return [

    'label' => 'Konfiguratu',

    'modal' => [

        'heading' => 'Posta elektronikoko egiaztapen kodeak konfiguratu',

        'description' => 'Saioa hastean edo ekintzak sentikorrak egitean postako bidali digun 6 digituko kodea sartu beharko duzu. Bilatu zure postakoan 6 digituko kodea konfigurazioa osatzeko.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Posta egiaztapen kodeak gaitu',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Posta egiaztapen kodeak aktibaturik',
        ],

    ],

];
