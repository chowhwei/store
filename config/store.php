<?php

return [
    'default' => 'toc',

    'store' => [
        'toc' => [
            'oss' => 'kj-file',
            'file' => 'data1'
        ]
    ],

    'oss' => [
        'kj-file' => [
            'oss_endpoint' => '',
            'oss_keyid' => '',
            'oss_keysecret' => '',
            'oss_bucket' => ''
        ]
    ],

    'file' => [
        'data1' => [
            'nfs_root' => '/data1'
        ]
    ]
];