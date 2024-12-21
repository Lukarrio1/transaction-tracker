<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class MailServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('mail.config', function ($app) {
            // Retrieve mail configuration settings from cache

            return [

            ];
        });
    }
}
