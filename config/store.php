<?php

return [
    'default' => 'default',

    'store' => [
        'default' => [
            'oss' => 'kj-file',
            'nas' => 'data1',
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
        'nas' => [
            'type' => 'nas',
            'root' => '/data1',
            'dir' => 'toc'
        ]
    ],

    'meta' => [
        'toc' => [
            'connection' => 'base_store',
            'table' => 'chapter_content'
        ]
    ]
];