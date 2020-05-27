<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Keep a change log 
    |--------------------------------------------------------------------------
    |
    */

    // Types of changelog entry
    'types' => [
        'added', 
        'changed', 
        'deprecated', 
        'removed', 
        'fixed', 
        'security'
    ],

    // Define repositories to allow the command line tool to create changelog files
    // in other directories
    'repositories' => [

        'default' =>  [
            'path' => base_path('') // directory for the CHANGELOG.md file
        ]

    ]

];
