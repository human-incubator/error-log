<?php

namespace HumanIncubator\ErrorLog;

use Illuminate\Support\ServiceProvider;

class ErrorLogServiceProvider extends ServiceProvider {
    public function boot() {
    }

    public function register() {
        $this->publishes([
            __DIR__.'/../config/errorlog.php' => config_path('errorlog.php'),
        ]);
    }
}
