<?php

return [
    'version' => '/v1',
    "api" => "https://api.celcoin.com.br",
    'api_homologation' => 'https://apihmlg.celcoin.com.br',
    'client_id' => 'ZtEZqsQoR8FzmEY',
    'client_secret' => '6ljt1Xo4w84tf0B',
    'debug' => false,

    /*
    | Principais Endpoints da platforma Celcoin
    |
    */
    "paths" => [
        'Token' => '/token',
        'Merchant' => '/merchant/info',
        'ProviderValues' => '/transactions/topups/provider_values',
        'Providers' => '/transactions/topups/providers',
        'Topup' => '/transactions/topups',
        'Transaction' => '/transactions',
    ],
];