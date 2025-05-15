<?php

namespace Workdo\RecurringInvoiceBill\Providers;

use Illuminate\Support\ServiceProvider;
use Workdo\RecurringInvoiceBill\Providers\EventServiceProvider;
use Workdo\RecurringInvoiceBill\Providers\RouteServiceProvider;
use Illuminate\Console\Scheduling\Schedule;


class RecurringInvoiceBillServiceProvider extends ServiceProvider
{

    protected $moduleName = 'RecurringInvoiceBill';
    protected $moduleNameLower = 'recurringinvoicebill';

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
        $this->commands([ \Workdo\RecurringInvoiceBill\Console\RecurringData::class,
    ]);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'recurring-invoice-bill');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerTranslations();

        $this->app->booted(function(){
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('recurring:invoice-bill')->daily();

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
