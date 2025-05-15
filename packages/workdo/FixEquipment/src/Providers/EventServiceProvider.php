<?php

namespace Workdo\FixEquipment\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as Provider;
use App\Events\CompanyMenuEvent;
use Workdo\FixEquipment\Listeners\CompanyMenuListener;
use Workdo\FixEquipment\Listeners\CreateAccessoriesLis;
use Workdo\FixEquipment\Listeners\CreateAssetLis;
use Workdo\FixEquipment\Listeners\CreateConsumablesLis;
use Workdo\FixEquipment\Listeners\CreateLicenceLis;
use Workdo\FixEquipment\Listeners\CreateMaintenanceLis;
use Workdo\FixEquipment\Events\CreateAccessories;
use Workdo\FixEquipment\Events\CreateAsset;
use Workdo\FixEquipment\Events\CreateConsumables;
use Workdo\FixEquipment\Events\CreateLicence;
use Workdo\FixEquipment\Events\CreateMaintenance;

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
        CreateAccessories::class => [
            CreateAccessoriesLis::class,
        ],
        CreateAsset::class => [
            CreateAssetLis::class,
        ],
        CreateConsumables::class => [
            CreateConsumablesLis::class,
        ],
        CreateLicence::class => [
            CreateLicenceLis::class,
        ],
        CreateMaintenance::class => [
            CreateMaintenanceLis::class,
        ]
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
