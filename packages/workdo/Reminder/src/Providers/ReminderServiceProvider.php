<?php

namespace Workdo\Reminder\Providers;

use Illuminate\Support\ServiceProvider;
use Workdo\Reminder\Providers\EventServiceProvider;
use Workdo\Reminder\Providers\RouteServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ReminderServiceProvider extends ServiceProvider
{

    protected $moduleName = 'Reminder';
    protected $moduleNameLower = 'reminder';

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->commands([ \Workdo\Reminder\Console\ReminderNotification::class,    ]);


    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'reminder');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerTranslations();
        $this->app->booted(function(){
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('reminder:notification')->daily();

        });
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(__DIR__.'/../Resources/lang');
        }
    }
}
