<?php declare(strict_types=1);

use Tulia\Cms\Node\Query\Enum\ScopeEnum;

return [
    'i18n' => [
        'translations' => [
            'source' => [
                dirname(__DIR__) . '/translations',
            ],
        ],
    ],
    'templating' => [
        'loader' => [
            'paths' => [
                'cms/node' => dirname(__DIR__) . '/views/frontend',
                'backend/node' => dirname(__DIR__) . '/views/backend',
            ],
        ],
    ],
    'cms_node' => [
        'finder' => [
            'content_renderer' => [
                'scopes' => [
                    ScopeEnum::SINGLE,
                    ScopeEnum::ROUTING_MATCHER,
                ],
            ],
        ],
    ],
];
