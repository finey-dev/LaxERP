<?php

namespace Workdo\Facilities\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as Provider;
use App\Events\CompanyMenuEvent;
use Workdo\ProductService\Events\CreateProduct;
use Workdo\ProductService\Events\DestroyProduct;
use Workdo\ProductService\Events\UpdateProduct;
use Workdo\Facilities\Listeners\CompanyMenuListener;
use Workdo\Facilities\Listeners\CreateProductLis;
use Workdo\Facilities\Listeners\DestroyProductLis;
use Workdo\Facilities\Listeners\UpdateProductLis;

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
        CreateProduct::class => [
            CreateProductLis::class,
        ],
        UpdateProduct::class => [
            UpdateProductLis::class,
        ],
        DestroyProduct::class => [
            DestroyProductLis::class,
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
