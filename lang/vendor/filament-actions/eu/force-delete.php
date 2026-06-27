<?php

return [

    'single' => [

        'label' => 'Indarrez ezabatu',

        'modal' => [

            'heading' => 'Indarrez ezabatu :label',

            'actions' => [

                'delete' => [
                    'label' => 'Ezabatu',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Ezabatuta',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Hautatutakoak indarrez ezabatu',

        'modal' => [

            'heading' => 'Indarrez ezabatu hautatutako :label',

            'actions' => [

                'delete' => [
                    'label' => 'Ezabatu',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Ezabatuta',
            ],

            'deleted_partial' => [
                'title' => 'Ezabatuta :count / :total',
                'missing_authorization_failure_message' => 'Ez duzu baimenik :count ezabatzeko.',
                'missing_processing_failure_message' => ':count ezin da ezabatu.',
            ],

            'deleted_none' => [
                'title' => 'Ezinezkoa ezabatu',
                'missing_authorization_failure_message' => 'Ez duzu baimenik :count ezabatzeko.',
                'missing_processing_failure_message' => ':count ezin da ezabatu.',
            ],

        ],

    ],

];
