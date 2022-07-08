<?php return [
    'cms.theme' => null,
    'cms.website' => [
        'backend_prefix' => '/administrator',
        'locales' => [
            [
                'locale_code' => 'en_US',
                'domain' => 'tulia.loc',
                'domain_development' => 'tulia.loc',
                'path_prefix' => null,
                'locale_prefix' => null,
                'default' => true,
                'ssl_mode' => 'ALLOWED_BOTH',
            ],
            [
                'locale_code' => 'pl_PL',
                'domain' => 'tulia.loc',
                'domain_development' => 'tulia.loc',
                'path_prefix' => null,
                'locale_prefix' => '/pl',
                'default' => false,
                'ssl_mode' => 'ALLOWED_BOTH',
            ],
        ],
    ],
];
