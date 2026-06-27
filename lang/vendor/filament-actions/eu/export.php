<?php

return [

    'label' => 'Esportatu :label',

    'modal' => [

        'heading' => 'Esportatu :label',

        'form' => [

            'columns' => [

                'label' => 'Zutabeak',

                'actions' => [

                    'select_all' => [
                        'label' => 'Hautatu guztiak',
                    ],

                    'deselect_all' => [
                        'label' => 'Hautapenak kendu',
                    ],

                ],

                'form' => [

                    'is_enabled' => [
                        'label' => ':column gaituta',
                    ],

                    'label' => [
                        'label' => ':column etiketa',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Esportatu',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Esportazioa osatuta',

            'actions' => [

                'download_csv' => [
                    'label' => '.csv deskargatu',
                ],

                'download_xlsx' => [
                    'label' => '.xlsx deskargatu',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Esportazioa gehiegi da',
            'body' => 'Behin batean ezin duzu errenkada bat baino gehiago esportatu.|Behin batean ezin duzu :count errenkada baino gehiago esportatu.',
        ],

        'no_columns' => [
            'title' => 'Ez da zutaberik hautatu',
            'body' => 'Mesedez, hautatu esportatzeko gutxienez zutabe bat.',
        ],

        'started' => [
            'title' => 'Esportazioa hasi da',
            'body' => 'Esportazioa hasi da eta 1 errenkada atzeko planoko prozesuan izango da. Bukatzean esteka duen jakinarazpena jasoko duzu.|Esportazioa hasi da eta :count errenkada atzeko planoko prozesuan izango dira. Bukatzean esteka duen jakinarazpena jasoko duzu.',
        ],

    ],

    'file_name' => 'esportazioa-:export_id-:model',

];
