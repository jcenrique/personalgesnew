<?php

return [

    'single' => [

        'label' => 'Berreskuratu',

        'modal' => [

            'heading' => 'Berreskuratu :label',

            'actions' => [

                'restore' => [
                    'label' => 'Berreskuratu',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Berreskuratuta',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Hautatutakoak berreskuratu',

        'modal' => [

            'heading' => 'Berreskuratu hautatutako :label',

            'actions' => [

                'restore' => [
                    'label' => 'Berreskuratu',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Berreskuratuta',
            ],

            'restored_partial' => [
                'title' => 'Berreskuratuta :count / :total',
                'missing_authorization_failure_message' => 'Ez duzu baimenik :count berreskuratzeko.',
                'missing_processing_failure_message' => ':count ezin da berreskuratu.',
            ],

            'restored_none' => [
                'title' => 'Berreskuratzea huts egin du',
                'missing_authorization_failure_message' => 'Ez duzu baimenik :count berreskuratzeko.',
                'missing_processing_failure_message' => ':count ezin da berreskuratu.',
            ],

        ],

    ],

];
