<?php

namespace Fatihirday\MailTemplate;

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
