<?php

return [
    'driver' => env('SCOUT_DRIVER', 'database'),
    'prefix' => env('SCOUT_PREFIX', ''),
    'queue' => env('SCOUT_QUEUE', true),
    'after_commit' => env('SCOUT_AFTER_COMMIT', true),
    'chunk' => [
        'searchable' => 200,
        'unsearchable' => 200,
    ],
    'soft_delete' => false,
    'meilisearch' => [
        'host' => env('MEILISEARCH_HOST', 'http://127.0.0.1:7700'),
        'key' => env('MEILISEARCH_KEY'),
        'index-settings' => [
            'tri_dharmas' => [
                'searchableAttributes' => [
                    'title',
                    'authors',
                    'abstract',
                ],
                'filterableAttributes' => [
                    'status',
                    'author_ids',
                    'category_id',
                    'document_type_id',
                    'faculty_id',
                    'study_program_id',
                    'publish_year',
                ],
                'sortableAttributes' => [
                    'publish_year',
                ],
            ],
        ],
    ],
];
