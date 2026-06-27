<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Izena',
    'column.guard_name' => 'Guard',
    'column.team' => 'Taldea',
    'column.roles' => 'Rolak',
    'column.permissions' => 'Baimenak',
    'column.updated_at' => 'Eguneratuta',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Izena',
    'field.guard_name' => 'Guard',
    'field.permissions' => 'Baimenak',
    'field.team' => 'Taldea',
    'field.team.placeholder' => 'Hautatu taldea ...',
    'field.select_all.name' => 'Denak hautatu',
    'field.select_all.message' => 'Aktibatu rol honetarako orain <span class="text-primary font-medium">aktibatuta</span> dauden baimen guztiak',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Filament Shield',
    'nav.role.label' => 'Rolak',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rola',
    'resource.label.roles' => 'Rolak',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Entitateak',
    'resources' => 'Baliabideak',
    'widgets' => 'Widget-ak',
    'pages' => 'Orriak',
    'custom' => 'Pertsonalizatutako baimenak',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Ez duzu sarbiderik',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

        'resource_permission_prefixes_labels' => [
            'view' => 'Erregistro bat bereziki ikusi',
            'view_any' => 'Erregistroen zerrenda ikusi',
            'create' => 'Sortu',
            'update' => 'Eguneratu',
            'delete' => 'Erregistro bat bereziki ezabatu',
            'delete_any' => 'Hainbat erregistro aldi berean ezabatu',
            'force_delete' => 'Erregistro bat bereziki indarrez ezabatu',
            'force_delete_any' => 'Hainbat erregistro indarrez ezabatu',
            'restore' => 'Erregistro bat bereziki leheneratu',
            'reorder' => 'Berrordenatu',
            'restore_any' => 'Hainbat erregistro leheneratu',
            'replicate' => 'Bikoiztu',
        ],


    ];
