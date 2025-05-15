<?php

namespace Workdo\MachineRepairManagement\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'MachineRepairManagement';
        $menu = $event->menu;
        $menu->add([
            'category' => 'General',
            'title' => __('Machine Repair'),
            'icon' => '',
            'name' => 'machine-repair-management-dashboard',
            'parent' => 'dashboard',
            'order' => 215,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'dashboard.machine.repair',
            'module' => $module,
            'permission' => 'machine dashboard manage'
        ]);
        $menu->add([
            'category' => 'Vehicle',
            'title' => __('Machine Repair'),
            'icon' => 'brand-appstore',
            'name' => 'machine-repair-management',
            'parent' => null,
            'order' => 713,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'machinerepairmanagement manage'
        ]);
        $menu->add([
            'category' => 'Vehicle',
            'title' => __('Machine'),
            'icon' => '',
            'name' => 'machine',
            'parent' => 'machine-repair-management',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'machine-repair.index',
            'module' => $module,
            'permission' => 'machine manage'
        ]);
        $menu->add([
            'category' => 'Vehicle',
            'title' => __('Repair Request'),
            'icon' => '',
            'name' => 'machine-repair-request',
            'parent' => 'machine-repair-management',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'machine-repair-request.index',
            'module' => $module,
            'permission' => 'repair request manage'
        ]);
        $menu->add([
            'category' => 'Vehicle',
            'title' => __('Repair History'),
            'icon' => '',
            'name' => 'repair-history',
            'parent' => 'machine-repair-management',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'machine-repair-history.index',
            'module' => $module,
            'permission' => 'machine repair history manage'
        ]);
        $menu->add([
            'category' => 'Vehicle',
            'title' => __('Service Agreement'),
            'icon' => '',
            'name' => 'service-agreement',
            'parent' => 'machine-repair-management',
            'order' => 25,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'machine-service-agreement.index',
            'module' => $module,
            'permission' => 'machine service agreement manage'
        ]);
    }
}
