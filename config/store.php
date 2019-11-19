<?php

return [
    'default' => 'toc',

    'store' => [
        'toc' => [
            'oss' => 'kj-file',
            'file' => 'data1'
        ]
    ],

    'client' => [
        'kj-file' => [
            'type' => 'oss',
            'oss_endpoint' => '',
            'oss_keyid' => '',
            'oss_keysecret' => '',
            'oss_bucket' => ''
        ],
        'data1' => [
            'type' => 'file',
            'nfs_root' => '/data1'
        ]
    ]
];