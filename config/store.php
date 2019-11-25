<?php

return [
    'default' => 'toc',

    'store' => [
        'toc' => [
            'oss' => 'kj-file',
            'file' => 'data1',
            'meta' => 'toc'
        ]
    ],

    'client' => [
        'kj-file' => [
            'type' => 'oss',
            'oss_endpoint' => '',
            'oss_keyid' => '',
            'oss_keysecret' => '',
            'oss_bucket' => '',
            'prefix' => 'toc'
        ],
        'data1' => [
            'type' => 'file',
            'nfs_root' => '/data1',
            'dir' => 'toc'
        ]
    ],

    'meta' => [
        'toc' => [
            'type' => 'keystore',
            'connection' => 'base_store',
            'table' => 'chapter_content'
        ]
    ]
];