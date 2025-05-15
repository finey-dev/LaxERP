<?php

namespace Workdo\SalesAgent\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as Provider;
use App\Events\CompanyMenuEvent;
use App\Events\CompanySettingEvent;
use App\Events\CompanySettingMenuEvent;
use Workdo\SalesAgent\Listeners\CompanyMenuListener;
use Workdo\SalesAgent\Listeners\CompanySettingListener;
use Workdo\SalesAgent\Listeners\CompanySettingMenuListener;
use App\Events\CreateInvoice;
use App\Events\UpdateRole;
use App\Events\CreateUser;
use Workdo\SalesAgent\Listeners\CreateInvoiceLis;
use Workdo\SalesAgent\Listeners\AddSalesAgentPermissions;
use Workdo\SalesAgent\Listeners\CreateSalesAgentRole;
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
        CompanySettingEvent::class => [
            CompanySettingListener::class,
        ],
        CompanySettingMenuEvent::class => [
            CompanySettingMenuListener::class,
        ],
        CreateInvoice::class => [
            CreateInvoiceLis::class
        ],
        UpdateRole::class => [
            AddSalesAgentPermissions::class
        ],
        CreateUser::class => [
            CreateSalesAgentRole::class
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
