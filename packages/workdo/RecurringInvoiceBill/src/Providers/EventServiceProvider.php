<?php

namespace Workdo\RecurringInvoiceBill\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as Provider;
use Workdo\RecurringInvoiceBill\Listeners\CompanySettingListener;
use Workdo\RecurringInvoiceBill\Listeners\CompanySettingMenuListener;
use Workdo\RecurringInvoiceBill\Listeners\SuperAdminSettingListener;
use Workdo\RecurringInvoiceBill\Listeners\SuperAdminSettingMenuListener;
use Workdo\RecurringInvoiceBill\Listeners\CreateRecurringBillLis;
use Workdo\RecurringInvoiceBill\Listeners\CreateRecurringInvoiceLis;
use Workdo\RecurringInvoiceBill\Listeners\DestroycurringBillLis;
use Workdo\RecurringInvoiceBill\Listeners\DestroycurringInvoiceLis;
use Workdo\RecurringInvoiceBill\Listeners\UpdateRecurringBillLis;
use Workdo\RecurringInvoiceBill\Listeners\UpdateRecurringInvoiceLis;
use App\Events\CompanySettingEvent;
use App\Events\CompanySettingMenuEvent;
use Workdo\Account\Events\CreateBill;
use App\Events\CreateInvoice;
use Workdo\Account\Events\DestroyBill;
use App\Events\DestroyInvoice;
use App\Events\SuperAdminSettingEvent;
use App\Events\SuperAdminSettingMenuEvent;
use Workdo\Account\Events\UpdateBill;
use App\Events\UpdateInvoice;

class EventServiceProvider extends Provider
{
    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    protected $listen = [
            CompanySettingEvent::class => [
            CompanySettingListener::class
            ],
            CompanySettingMenuEvent::class => [
            CompanySettingMenuListener::class
            ],
            SuperAdminSettingEvent::class => [
                SuperAdminSettingListener::class,
            ],
            SuperAdminSettingMenuEvent::class => [
                SuperAdminSettingMenuListener::class,
            ],
            CreateBill::class => [
            CreateRecurringBillLis::class
            ],
            CreateInvoice::class => [
            CreateRecurringInvoiceLis::class
            ],
            DestroyBill::class => [
            DestroycurringBillLis::class
            ],
            DestroyInvoice::class => [
            DestroycurringInvoiceLis::class
            ],
            UpdateBill::class => [
            UpdateRecurringBillLis::class
            ],
            UpdateInvoice::class => [
            UpdateRecurringInvoiceLis::class
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
