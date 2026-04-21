<?php

return [

    'default_provider' => env('AI_DEFAULT_PROVIDER', 'openai'),

    'providers' => [
        'openai' => [
            'model' => env('AI_OPENAI_MODEL', 'gpt-4.1-mini'),
            'embedding_model' => env('AI_OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'),
        ],
    ],

    'request_logging' => [
        'enabled' => env('AI_REQUEST_LOGGING_ENABLED', true),
    ],

];
