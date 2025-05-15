<?php

namespace Workdo\BiometricAttendance\Providers;

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
        view()->composer(['hrm::employee.edit', 'hrm::employee.create', 'recruitment::jobApplication.convert'], function ($view) {
            if (\Auth::check()) {
                $view->getFactory()->startPush('biometric_emp_id', view('biometric-attendance::attendance.biometric_emp_id'));
            }
        });
    }

    public function register()
    {
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
