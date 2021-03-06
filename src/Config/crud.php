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
    'useUuids' => false, // Requires https://github.com/webpatser/laravel-uuid, If you turn this on/off you will need to update your migrations for any existing models
    'fieldTypes' => [ // Uncomment any fields you need, comment any you don't like
        'string',
        'bigIncrements',
        'bigInteger',
        'binary',
        'boolean',
        'char',
        'date',
        'dateTime',
        'dateTimeTz',
        'decimal',
        'double',
        'enum',
        'float',
        'geometry',
        'geometryCollection',
        'increments',
        'integer',
        'ipAddress',
        'json',
        'jsonb',
        'lineString',
        'longText',
        'macAddress',
        'mediumIncrements',
        'mediumInteger',
        'mediumText',
        'morphs',
        'multiLineString',
        'multiPoint',
        'multiPolygon',
        'nullableMorphs',
        'nullableTimestamps',
        'point',
        'polygon',
        'rememberToken',
        'smallIncrements',
        'smallInteger',
        'softDeletes',
        'softDeletesTz',
        'text',
        'time',
        'timeTz',
        'timestamp',
        'timestamps',
        'timestampsTz',
        'tinyIncrements',
        'tinyInteger',
        'unsignedBigInteger',
        'unsignedDecimal',
        'unsignedInteger',
        'unsignedMediumInteger',
        'unsignedSmallInteger',
        'unsignedTinyInteger',
        'uuid',
        'year'
    ],
    'validators' => [
        'accepted',
        'active_url',
        //'after:date',
        //'after_or_equal:date',
        'alpha',
        'alpha_dash',
        'alpha_num',
        'array',
        //'before:date',
        //'before_or_equal:date',
        //'between:min,max',
        'boolean',
        'confirmed',
        'date',
        //'date_equals:date',
        //'date_format:format',
        //'different:field',
        //'digits:value',
        //'digits_between:min,max',
        'dimensions',
        'distinct',
        'email',
        //'exists:table,column',
        'file',
        'filled',
        'image',
        //'in:foo,bar,...',
        //'in_array:anotherfield',
        'integer',
        'ip',
        'ipv4',
        'ipv6',
        'json',
        //'max:value',
        //'mimetypes:text/plain,...',
        //'mimes:foo,bar,...',
        //'min:value',
        'nullable',
        //'not_in:foo,bar,...',
        'numeric',
        'present',
        //'regex:pattern',
        'required',
        //'required_if:anotherfield,value,...',
        //'required_unless:anotherfield,value,...',
        //'required_with:foo,bar,...',
        //'required_with_all:foo,bar,...',
        //'required_without:foo,bar,...',
        //'required_without_all:foo,bar,...',
        //'same:field',
        //'size:value',
        'string',
        'timezone',
        //'unique:table,column,except,idColumn',
        'url'
    ]
];