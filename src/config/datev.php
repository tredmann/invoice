<?php

return [
    'clientId' => env('DATEV_CLIENT_ID'),
    'clientSecret' => env('DATEV_CLIENT_SECRET'),
    'urlAuthorize'            => 'https://login.datev.de/openidsandbox/authorize',
    'urlAccessToken'          => 'https://sandbox-api.datev.de/token',
    'urlResourceOwnerDetails' => 'https://login.datev.de/openidsandbox'
];
