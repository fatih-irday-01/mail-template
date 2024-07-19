<?php

namespace Fatihirday\MailTemplate;

use Fatihirday\MailTemplate\Console\MailCacheClear;
use Illuminate\Support\ServiceProvider;

class MailTemplateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                MailCacheClear::class
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__.'/config/mail-template.php' => config_path('mail-template.php'),
        ], 'config');
    }
}
