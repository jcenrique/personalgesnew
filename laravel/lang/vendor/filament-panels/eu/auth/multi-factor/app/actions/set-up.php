<?php

return [

    'label' => 'Konfiguratu',

    'modal' => [

        'heading' => 'Autentifikazioa aplikazioa konfiguratu',

        'description' => <<<'BLADE'
            Google Authenticator-en bezalako aplikazio bat beharko duzu (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) prozesu hau osatzeko.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'QR kode hau eskaneatu zure autentifikazioa aplikazioaz:',

                'alt' => 'Autentifikazioa aplikazioaz eskaneatzeko QR kodea',

            ],

            'text_code' => [

                'instruction' => 'Edo kode hau sartu eskuz:',

                'messages' => [
                    'copied' => 'Kopiatua',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Ondorengo berreskurapen kodeak leku seguru batean gordetzea. Behin bakarrik erakutsiko dira, eta beharko duzu baldin eta zure autentifikazioa aplikaziora sarbidea galtzen baduzu:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Autentifikazioaren aplikazioko 6 digituko kodea sartu',

                'validation_attribute' => 'kodea',

                'below_content' => 'Saioa hastean edo ekintzak sentikorrak egitean zure autentifikazioa aplikazioaren 6 digituko kodea sartu beharko duzu.',

                'messages' => [

                    'invalid' => 'Sarturiko kodea ez da baliogarria.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Autentifikazioa aplikazioa gaitu',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Autentifikazioa aplikazioa aktibaturik',
        ],

    ],

];
