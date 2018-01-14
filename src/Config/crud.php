<?php

return [
    'limit' => 25,
    'tableNormalizationExceptions' => [],
    'routing' => [
        'newbb' => [],
        'users' => [],
        'examples' => [],
        'mtmexamples' => [
            'name' => 'mtm_examples'
        ]
    ],
    'namespaces' => [
        'models' => "\\App\\",
        'repositories' => "\\App\\Repositories\\",
        'factories' => "\\App\\Factories\\",
        'transformers' => "\\App\\Transformers\\"
    ],
    'interfaces' => [
        'html' => [
            'enabled' => true,
            'middleware' => ['auth']
        ],
        'json' => [
            'enabled' => true,
            'middleware' => ['auth:api']
        ]
    ],
    'overrideAuthViews' => true,
    'useSubnav' => false,
    'useUuids' => false // Requires https://github.com/webpatser/laravel-uuid
                        // If you turn this on/off you will need to update your migrations for any existing models
];