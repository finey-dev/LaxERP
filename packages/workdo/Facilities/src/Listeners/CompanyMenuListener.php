<?php

namespace Workdo\Facilities\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Facilities';
        $menu = $event->menu;
        $menu->add([
            'category' => 'General',
            'title' => __('Facilities Dashboard'),
            'icon' => '',
            'name' => 'facilities-dashboard',
            'parent' => 'dashboard',
            'order' => 170,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'facilities.dashboard',
            'module' => $module,
            'permission' => 'facilities dashboard manage'
        ]);
        $menu->add([
            'category' => 'eCommerce',
            'title' => __('Facilities'),
            'icon' => 'adjustments',
            'name' => 'facility',
            'parent' => null,
            'order' => 697,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'facilities booking manage'
        ]);
        $menu->add([
            'category' => 'eCommerce',
            'title' => __('Booking'),
            'icon' => '',
            'name' => 'booking',
            'parent' => 'facility',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'facility-booking.index',
            'module' => $module,
            'permission' => 'facilitiesbooking manage'
        ]);
        $menu->add([
            'category' => 'eCommerce',
            'title' => __('Booking Orders'),
            'icon' => '',
            'name' => 'booking-orders',
            'parent' => 'facility',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'facility-booking-order.index',
            'module' => $module,
            'permission' => 'facilities booking order manage'
        ]);
        $menu->add([
            'category' => 'eCommerce',
            'title' => __('Booking Receipt'),
            'icon' => '',
            'name' => 'booking-receipt',
            'parent' => 'facility',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'facilities.booking.receipt',
            'module' => $module,
            'permission' => 'facilities booking order manage'
        ]);
        $menu->add([
            'category' => 'eCommerce',
            'title' => __('System Setup'),
            'icon' => '',
            'name' => 'facilities-settings',
            'parent' => 'facility',
            'order' => 25,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'facilities settings manage'
        ]);
        $menu->add([
            'category' => 'eCommerce',
            'title' => __('Service'),
            'icon' => '',
            'name' => 'service',
            'parent' => 'facilities-settings',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'facilities-service.index',
            'module' => $module,
            'permission' => 'facilitiesservice manage'
        ]);
        $menu->add([
            'category' => 'eCommerce',
            'title' => __('Spaces'),
            'icon' => '',
            'name' => 'spaces',
            'parent' => 'facilities-settings',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'facilities-space.index',
            'module' => $module,
            'permission' => 'facilitiesspace manage'
        ]);
        $menu->add([
            'category' => 'eCommerce',
            'title' => __('Working Hours'),
            'icon' => '',
            'name' => 'working-hours',
            'parent' => 'facilities-settings',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'facilities-working.index',
            'module' => $module,
            'permission' => 'facilitiesworking manage'
        ]);
    }
}
