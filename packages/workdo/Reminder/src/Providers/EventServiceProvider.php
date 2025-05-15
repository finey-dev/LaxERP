<?php

namespace Workdo\Reminder\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as Provider;
use App\Events\CompanyMenuEvent;
use App\Events\CompanySettingEvent;
use App\Events\CompanySettingMenuEvent;
use App\Events\SuperAdminSettingEvent;
use App\Events\SuperAdminSettingMenuEvent;
use Workdo\Reminder\Listeners\CompanyMenuListener;
use Workdo\Reminder\Listeners\CompanySettingListener;
use Workdo\Reminder\Listeners\CompanySettingMenuListener;
use App\Events\UpdateInvoice;
use Workdo\Reminder\Listeners\SuperAdminSettingListener;
use Workdo\Reminder\Listeners\SuperAdminSettingMenuListener;
use Workdo\Lead\Events\UpdateLead;
use Workdo\Account\Events\UpdateBill;
use Workdo\Reminder\Listeners\UpdateInvoiceLis;
use Workdo\Reminder\Listeners\UpdateLeadLis;
use Workdo\Reminder\Listeners\UpdateBillLis;
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
        SuperAdminSettingEvent::class => [
            SuperAdminSettingListener::class,
        ],
        SuperAdminSettingMenuEvent::class => [
            SuperAdminSettingMenuListener::class,
        ],
        UpdateInvoice::class => [
            UpdateInvoiceLis::class,
        ],
        UpdateLead::class =>[
            UpdateLeadLis::class,
        ],
        UpdateBill::class =>[
            UpdateBillLis::class,
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
