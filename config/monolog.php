<?php

return [
    'name' => 'box-sdk',
    'handlers' => [
        'default' => [
            'type' => 'rotating_file',
            'path' => 'var/log/box-sdk.log',
            'level' => 'debug',
            'max_files' => 5,
            'max_file_size' => 100 * 1024 * 1024, // 100MB
            'levels' => ['debug', 'info'],
        ],
        'warning' => [
            'type' => 'rotating_file',
            'path' => 'var/log/box-sdk-warning.log',
            'level' => 'warning',
            'max_files' => 5,
            'max_file_size' => 100 * 1024 * 1024,
            'levels' => ['warning'],
        ],
        'error' => [
            'type' => 'rotating_file',
            'path' => 'var/log/box-sdk-error.log',
            'level' => 'error',
            'max_files' => 5,
            'max_file_size' => 100 * 1024 * 1024,
            'levels' => ['error'],
        ],
    ],
    'log_dir' => 'var/log',
    'log_file_basename' => 'box-sdk.log',
];
