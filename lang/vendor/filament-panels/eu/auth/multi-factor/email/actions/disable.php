<?php

return [

    'label' => 'Itzali',

    'modal' => [

        'heading' => 'Posta egiaztapen kodeak desgaitu',

        'description' => 'Ziur al zaude posta egiaztapen kodeak jasotzea utzitu nahi duzula? Desgaitzeak zure kontua segurtasunerako geruza bat kendu egingo du.',

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
                'label' => 'Posta egiaztapen kodeak desgaitu',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Posta egiaztapen kodeak desaktibaturik',
        ],

    ],

];
