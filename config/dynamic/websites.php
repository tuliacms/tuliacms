<?php

return [
    [
        'id' => 'f19b16b2-f52b-442a-aee2-8e0f4fed31b7',
        'backend_prefix' => '/administrator',
        'name' => 'Default website',
        'code' => 'en_US',
        'domain' => $_SERVER['HTTP_HOST'] ?? 'tulia.loc',
        'locale_prefix' => null,
        'path_prefix' => '/tulia',
        'ssl_mode' => 'ALLOWED_BOTH',
        'default' => true,
    ],
    [
        'id' => 'f19b16b2-f52b-442a-aee2-8e0f4fed31b7',
        'backend_prefix' => '/administrator',
        'name' => 'Default website',
        'code' => 'pl_PL',
        'domain' => $_SERVER['HTTP_HOST'] ?? 'tulia.loc',
        'locale_prefix' => '/pl',
        'path_prefix' => '/tulia',
        'ssl_mode' => 'ALLOWED_BOTH',
        'default' => false,
    ],
];
