<?php

namespace Workdo\WordpressWoocommerce\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as Provider;
use App\Events\CompanyMenuEvent;
use App\Events\CompanySettingEvent;
use App\Events\CompanySettingMenuEvent;
use Workdo\ProductService\Events\DestroyCategory;
use Workdo\ProductService\Events\DestroyProduct;
use Workdo\WordpressWoocommerce\Listeners\CategoryDestroy;
use Workdo\WordpressWoocommerce\Listeners\CompanyMenuListener;
use Workdo\WordpressWoocommerce\Listeners\CompanySettingListener;
use Workdo\WordpressWoocommerce\Listeners\CompanySettingMenuListener;
use Workdo\WordpressWoocommerce\Listeners\ProductDestroy;
use Workdo\ProductService\Events\DestroyTax;
use Workdo\WordpressWoocommerce\Listeners\TaxDestroy;

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
        DestroyCategory::class => [
            CategoryDestroy::class,
        ],
        DestroyProduct::class => [
            ProductDestroy::class,
        ],
        DestroyTax::class => [
            TaxDestroy::class,
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
