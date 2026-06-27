<?php

return [

    'label' => 'Berreskurapen kodeak berregitea',

    'modal' => [

        'heading' => 'Autentifikazioa aplikazioaren berreskurapen kodeak berregitea',

        'description' => 'Zure berreskurapen kodeak galtzen baduzu, hemen berregitea ditzakezu. Zure zaharreko berreskurapen kodeak berehala baliogabetu egingo dira.',

        'form' => [

            'code' => [

                'label' => 'Autentifikazioaren aplikazioko 6 digituko kodea sartu',

                'validation_attribute' => 'kodea',

                'messages' => [

                    'invalid' => 'Sarturiko kodea ez da baliogarria.',

                ],

            ],

            'password' => [

                'label' => 'Edo zure uneko pasahitza sartu',

                'validation_attribute' => 'pasahitza',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Berreskurapen kodeak berregitea',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Autentifikazioa aplikazioaren berreskurapen kode berriak sortu dira',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Berreskurapen kode berriak',

            'description' => 'Ondorengo berreskurapen kodeak leku seguru batean gordetzea. Behin bakarrik erakutsiko dira, eta beharko duzu baldin eta zure autentifikazioa aplikaziora sarbidea galtzen baduzu:',

            'actions' => [

                'submit' => [
                    'label' => 'Itxi',
                ],

            ],

        ],

    ],

];
