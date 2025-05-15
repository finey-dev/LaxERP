<?php

namespace Workdo\RepairManagementSystem\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{

    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'RepairManagementSystem';
        $menu = $event->menu;
        $menu->add([
            'category' =>'Vehicle',
            'title' => __('Repair'),
            'icon' => 'tool',
            'name' => 'repair-management-system',
            'parent' => null,
            'order' => 714,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'repair manage'
        ]);
        $menu->add([
            'category' =>'Vehicle',
            'title' =>  __('Repair Order Request'),
            'icon' => '',
            'name' => 'repair-order-request',
            'parent' => 'repair-management-system',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'repair.request.index',
            'module' => $module,
            'permission' => 'repair order request manage'
        ]);
        $menu->add([
            'category' =>'Vehicle',
            'title' => __('Repair Invoice'),
            'icon' => '',
            'name' => 'repair-invoice',
            'parent' => 'repair-management-system',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'repair.request.invoice.index',
            'module' => $module,
            'permission' => 'repair invoice manage'
        ]);
        $menu->add([
            'category' =>'Vehicle',
            'title' => __('Repair Technicians'),
            'icon' => '',
            'name' => 'repair-technicians',
            'parent' => 'repair-management-system',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'repair-technician.index',
            'module' => $module,
            'permission' => 'repair technician manage'
        ]);
        $menu->add([
            'category' =>'Vehicle',
            'title' => __('Warranties'),
            'icon' => '',
            'name' => 'warranties',
            'parent' => 'repair-management-system',
            'order' => 25,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'repair-warranty.index',
            'module' => $module,
            'permission' => 'warranty manage'
        ]);
    }
}
