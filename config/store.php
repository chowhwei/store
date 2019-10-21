<?php

return [
    'default' => 'aliyunoss',

    'storages' => [
        'aliyunoss' => [
            'driver' => 'aliyunoss',

            'endpoint' => '',
            'username' => '',
            'password' => ''
        ],

        'file' => [
            'driver' => 'file',
            'path' => '/data0/path/to/file'
        ]
    ]
];