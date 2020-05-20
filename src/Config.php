<?php

return [
    'version' => '/v4',
    "api" => "https://api.celcoin.com.br",
    'api_homologation' => 'https://sandbox-apicorp.celcoin.com.br',
    'client_id' => 'teste',
    'client_secret' => 'teste',
    'debug' => false,

    /*
    | Endpoints utilizados - Celcoin
    |
    */
    "paths" => [
        'Providers' => '/transactions/topups/providers',
        'ProviderValues' => '/transactions/topups/provider-values',
        'FindProvider' => '/transactions/topups/find-providers',
        'Topup' => '/transactions/topups',
        'StatusTransaction' => '/transactions/status-consult'
        // 'Token' => '/token',
        // 'Merchant' => '/merchant/info',
        // 'Transaction' => '/transactions',
    ],
];