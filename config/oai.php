<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OAI-PMH (Open Archives Initiative Protocol for Metadata Harvesting)
    |--------------------------------------------------------------------------
    |
    | Configuration for the public OAI-PMH endpoint.
    |
    */

    'repository_name' => env('OAI_REPOSITORY_NAME', (string) config('app.name')),

    // Public contact for harvesters (required by OAI-PMH Identify)
    'admin_email' => env('OAI_ADMIN_EMAIL', (string) (config('mail.from.address') ?? 'admin@example.com')),

    // Used in OAI identifiers: oai:{repository_identifier}:article/{id}
    'repository_identifier' => env('OAI_REPOSITORY_IDENTIFIER', 'repository.univbatam.ac.id'),

    // Dublin Core defaults
    'publisher' => env('OAI_PUBLISHER', (string) config('app.name')),
    'language' => env('OAI_LANGUAGE', (string) config('app.locale', 'id')),

    // ListRecords page size (resumptionToken is used for paging)
    'max_records' => (int) env('OAI_MAX_RECORDS', 200),

    // Token signing for resumptionToken
    'token_secret' => env('OAI_TOKEN_SECRET', (string) config('app.key')),

    // OAI-PMH Identify response capabilities
    'deleted_record' => env('OAI_DELETED_RECORD', 'no'), // no|persistent|transient
    'granularity' => env('OAI_GRANULARITY', 'YYYY-MM-DDThh:mm:ssZ'),

    // Resource naming within the OAI identifier
    'resource_type' => env('OAI_RESOURCE_TYPE', 'article'),
];
