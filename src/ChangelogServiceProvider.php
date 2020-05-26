<?php

namespace AMBERSIVE\KeepAChangelog;

use Illuminate\Support\ServiceProvider;

use AMBERSIVE\Classes\Types;

class ChangelogServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
       
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Commands
        if ($this->app->runningInConsole()) {
            $this->commands([
               \AMBERSIVE\KeepAChangelog\Console\Commands\Dev\KeepAChangelogAdd::class,
               \AMBERSIVE\KeepAChangelog\Console\Commands\Dev\KeepAChangelogRelease::class
            ]);
        }

        // Configs
        $this->publishes([
            __DIR__.'/Configs/keepachangelog.php'         => config_path('keepachangelog.php'),
        ],'keepachangelog');

    }

}
