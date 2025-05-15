<?php
namespace Workdo\RecurringInvoiceBill\Listeners;
use App\Events\SuperAdminSettingMenuEvent;

class SuperAdminSettingMenuListener
{
    /**
     * Handle the event.
     */

    public function handle(SuperAdminSettingMenuEvent $event): void
    {
        $module = 'RecurringInvoiceBill';
        $menu = $event->menu;
        $menu->add([
            'title' => __('Recurring Invoice & Bill Settings'),
            'name' => 'recurringinvoicebill',
            'order' => 115,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'navigation' => 'sidenav-recurringinvoicebill',
            'module' => $module,
            'permission' => 'invoice bill recurring'
        ]);
    }



}
