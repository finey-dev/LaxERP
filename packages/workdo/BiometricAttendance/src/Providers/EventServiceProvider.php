<?php

namespace Workdo\BiometricAttendance\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as Provider;
use App\Events\CompanyMenuEvent;
use App\Events\CompanySettingEvent;
use App\Events\CompanySettingMenuEvent;
use Workdo\BiometricAttendance\Listeners\CompanyMenuListener;
use Workdo\BiometricAttendance\Listeners\CompanySettingListener;
use Workdo\BiometricAttendance\Listeners\CompanySettingMenuListener;
use Workdo\BiometricAttendance\Listeners\CreateBiometricEmpId;
use Workdo\Hrm\Events\CreateEmployee;
use Workdo\Hrm\Events\UpdateEmployee;
use Workdo\Recruitment\Events\ConvertToEmployee;

class EventServiceProvider extends Provider
{
    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    protected $listen = [
        CompanyMenuEvent::class => [
            CompanyMenuListener::class,
        ],

        CreateEmployee::class => [
            CreateBiometricEmpId::class
        ],
        
        UpdateEmployee::class => [
            CreateBiometricEmpId::class
        ],

        ConvertToEmployee::class => [
            CreateBiometricEmpId::class
        ],
    ];

    /**
     * Get the listener directories that should be used to discover events.
     *
     * @return array
     */
    protected function discoverEventsWithin()
    {
        return [
            __DIR__ . '/../Listeners',
        ];
    }
}
