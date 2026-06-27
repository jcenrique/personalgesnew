<?php

return [

    'title' => 'Egiaztatu zure posta helbidea',

    'heading' => 'Egiaztatu zure posta helbidea',

    'actions' => [

        'resend_notification' => [
            'label' => 'Bidali berriro',
        ],

    ],

    'messages' => [
        'notification_not_received' => '¿Ez duzu jaso bidalitako postako mezua?',
        'notification_sent' => ':email-ra posta mezua bidali dugu zure posta helbidea egiaztatzeko instrukzioekin.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Postako mezua bidali dugu berriro.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Bidalketako saiakera gehiegi',
            'body' => 'Mesedez, saiatu berriro :seconds segundotan.',
        ],

    ],

];
