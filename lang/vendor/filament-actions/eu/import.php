<?php

return [

    'label' => 'Inportatu :label',

    'modal' => [

        'heading' => 'Inportatu :label',

        'form' => [

            'file' => [

                'label' => 'Fitxategia',

                'placeholder' => 'Igo CSV fitxategi bat',

                'rules' => [
                    'duplicate_columns' => '{0} Fitxategiak ez du zutabe goiburua hutsa baino gehiago eduki behar.|{1,*} Fitxategiak ez du zutabe goiburua bikoitz eduki behar: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Zutabeak',
                'placeholder' => 'Hautatu zutabe bat',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Deskargatu CSV adibide fitxategia',
            ],

            'import' => [
                'label' => 'Inportatu',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Inportazioa osatuta',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Deskargatu informazioa huts egin duen errenkadari buruz|Deskargatu informazioa huts egin duten errenkadei buruz',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'CSV fitxategia handiegia da',
            'body' => 'Behin batean ezin duzu errenkada bat baino gehiago inportatu.|Behin batean ezin duzu :count errenkada baino gehiago inportatu.',
        ],

        'started' => [
            'title' => 'Inportazioa hasi da',
            'body' => 'Inportazioa hasi da eta 1 errenkada atzeko planoko prozesuan izango da. Bukatzean jakinarazpena jasoko duzu.|Inportazioa hasi da eta :count errenkada atzeko planoko prozesuan izango dira. Bukatzean jakinarazpena jasoko duzu.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-adibidea',
    ],

    'failure_csv' => [
        'file_name' => 'inportazioa-:import_id-:csv_izena-huts-egindako-errenkadak',
        'error_header' => 'errorea',
        'system_error' => 'Sistema errorea, jarri harremanetan laguntzarekin.',
        'column_mapping_required_for_new_record' => 'Zutabea ":attribute" ez da fitxategiko zutabe batekin mapatu, baina beharrezkoa da erregistro berriak sortzeko.',
    ],

];
