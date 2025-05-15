<?php

namespace Workdo\Sales\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as Provider;
use App\Events\CompanyMenuEvent;
use App\Events\CompanySettingEvent;
use App\Events\CompanySettingMenuEvent;
use App\Events\DefaultData;
use App\Events\DeleteProductService;
use App\Events\GivePermissionToRole;
use Workdo\Sales\Listeners\CompanyMenuListener;
use Workdo\Sales\Listeners\CompanySettingListener;
use Workdo\Sales\Listeners\CompanySettingMenuListener;
use Workdo\Sales\Listeners\DataDefault;
use Workdo\Sales\Listeners\GiveRoleToPermission;
use Workdo\Sales\Listeners\ProductServiceDelete;

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
        DefaultData::class => [
            DataDefault::class
        ],
        GivePermissionToRole::class => [
            GiveRoleToPermission::class
        ],
        DeleteProductService::class => [
            ProductServiceDelete::class
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
