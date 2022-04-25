<?php

return [
    'api_url'      => env('ERROR_LOG_LOCAL_API_URL', 'http://127.0.0.1:8000'),
    'token'        => env('ERROR_LOG_TOKEN'),
    'mode'         => env('ERROR_LOG_MODE', 'test'),
];