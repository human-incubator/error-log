<?php

namespace HumanIncubator\ErrorLog;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class ErrorLog {
    static $errorlog_url;
    static $client_api_key;
    static $mode;

    private static function init() {
        self::$client_api_key = Config::get('errorlog.token');
        $mode = Config::get('errorlog.mode');

        if ($mode == 'production') {
            self::$errorlog_url = 'https://api.centralized-error.com';
        }

        if ($mode == 'test') {
            self::$errorlog_url = 'https://api.centralized-error.hiro-test.net';
        }

        if ($mode == 'development') {
            self::$errorlog_url = Config::get('errorlog.api_url');
        }

        self::$mode = $mode;
    }

    public static function log(string $message, $error_code = null, $category = null) {
        self::init();

        try {
            $params = [
                'message' => $message,
                'category' => $category,
                'error_code' => $error_code,
            ];

            $response = Http::withToken(self::$client_api_key)->post(self::$errorlog_url . "/client/log", $params);
            
            if ($response->failed()) {
                return [
                    'error' => true,
                    'status' => $response->status(),
                    'message' => json_decode($response->body())
                ];
            } else {
                return json_decode($response);
            }
        } catch (\Throwable $th) {
            return [
                'error' => true,
                'status' => 500,
                'message' => ['code' => $th->getCode(), 'message'=> $th->getMessage()]
            ];
        }
    }
}
