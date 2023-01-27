<?php

namespace HumanIncubator\ErrorLog;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class ErrorLog {
    static $errorlog_url;
    static $client_api_key;
    static $mode;

    private static function init() {
        self::$client_api_key = Config::get('errorlog.token');
        $mode = Config::get('errorlog.mode');

        if ($mode == 'production') {
            self::$errorlog_url = 'https://errors-api.human-incubator.com';
        }

        if ($mode == 'test') {
            self::$errorlog_url = 'https://centralized-error-api.hiro-test.net';
        }

        if ($mode == 'development') {
            self::$errorlog_url = Config::get('errorlog.api_url');
        }

        self::$mode = $mode;
    }

    public static function log(string $message, $username = null, $error_code = null, $category = null, $exception_trace = null) {
        self::init();

        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;

        try {
            $params = [
                'message' => $message,
                'user_name' => $username,
                'category' => $category,
                'error_code' => $error_code,
                'user_agent' => $user_agent,
                'exception_trace' => $exception_trace
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

    public static function logByObject(array $param) {
        self::init();
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
  
        try {
            $params = [
                'message' => $param['message'],
                'user_name' => $param['user_name'],
                'category' => $param['category'],
                'error_code' => $param['error_code'],
                'user_agent' => $user_agent,
                'exception_trace' => $param['exception_trace'],
                'severity' => $param['severity']
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
