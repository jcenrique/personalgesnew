<?php

return [

    'single' => [

        'label' => 'Ezabatu',

        'modal' => [

            'heading' => 'Ezabatu :label',

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

        'label' => 'Hautatutakoak ezabatu',

        'modal' => [

            'heading' => 'Hautatutako :label ezabatu',

            'actions' => [

                'delete' => [
                    'label' => 'Ezabatu',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Deleted',
            ],

            'deleted_partial' => [
                'title' => 'Deleted :count of :total',
                'missing_authorization_failure_message' => 'You don\'t have permission to delete :count.',
                'missing_processing_failure_message' => ':count could not be deleted.',
            ],

            'deleted_none' => [
                'title' => 'Ezinezkoa ezabatu',
                'missing_authorization_failure_message' => 'You don\'t have permission to delete :count.',
                'missing_processing_failure_message' => ':count could not be deleted.',
            ],

        ],

    ],

];
