<?php

namespace Workdo\MachineRepairManagement\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class ViewComposer extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */

    public function boot()
    {
        view()->composer(['invoice.create','invoice.edit','invoice.index','invoice.grid'], function ($view){
            if (Auth::check() && module_is_active('MachineRepairManagement')){
                $view->getFactory()->startPush('account_type', view('machine-repair-management::invoice.account_type'));
            }
        });
    }
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
