<?php

return [
    'default' => 'aliyunoss',

    'storages' => [
        'aliyunoss' => [
            'driver' => 'aliyunoss',

            'auth' => [
                'endpoint' => 'http://oss-cn-hangzhou-internal.aliyuncs.com',
                'key_id' => '',
                'key_secret' => ''
            ]
        ],

        'file' => [
            'driver' => 'file',

            'dir' => '/data0/path/to/file'
        ]
    ]
];