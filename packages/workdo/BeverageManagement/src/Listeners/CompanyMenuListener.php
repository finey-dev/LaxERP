<?php

namespace Workdo\BeverageManagement\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'BeverageManagement';
        $menu = $event->menu;
        $menu->add([
            'category' => 'Operations',
            'title' => __('Beverages'),
            'icon' => 'ti ti-glass-full',
            'name' => 'beverage-management',
            'parent' => null,
            'order' => 697,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'beverage dashboard manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Collection Center'),
            'icon' => '',
            'name' => 'collection-center',
            'parent' => 'beverage-management',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'collection-center.index',
            'module' => $module,
            'permission' => 'collection center manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Raw Material'),
            'icon' => '',
            'name' => 'raw-material',
            'parent' => 'beverage-management',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'raw-material.index',
            'module' => $module,
            'permission' => 'raw material manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Bill Of Material'),
            'icon' => '',
            'name' => 'bill-of-material',
            'parent' => 'beverage-management',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'bill-of-material.index',
            'module' => $module,
            'permission' => 'bill of material manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Manufacturing'),
            'icon' => '',
            'name' => 'manufacturing',
            'parent' => 'beverage-management',
            'order' => 25,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'manufacturing.index',
            'module' => $module,
            'permission' => 'manufacturing manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Packaging'),
            'icon' => '',
            'name' => 'packaging',
            'parent' => 'beverage-management',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'packaging.index',
            'module' => $module,
            'permission' => 'packaging manage'
        ]);

        $menu->add([
            'category' => 'Operations',
            'title' => __('Quality Control'),
            'icon' => '',
            'name' => 'quality-control',
            'parent' => 'beverage-management',
            'order' => 35,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'sidebar qualitycontrol manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Quality Checks'),
            'icon' => '',
            'name' => 'quality-checks',
            'parent' => 'quality-control',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'quality-checks.index',
            'module' => $module,
            'permission' => 'quality-checks manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Quality Standards'),
            'icon' => '',
            'name' => 'quality-standards',
            'parent' => 'quality-control',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'quality-standards.index',
            'module' => $module,
            'permission' => 'quality-standards manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Maintenance'),
            'icon' => '',
            'name' => 'maintenance',
            'parent' => 'beverage-management',
            'order' => 40,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'beverage-maintenance.index',
            'module' => $module,
            'permission' => 'beverage-maintenance manage'
        ]);

        $menu->add([
            'category' => 'Operations',
            'title' => __('Waste Records'),
            'icon' => '',
            'name' => 'waste-records',
            'parent' => 'beverage-management',
            'order' => 45,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'waste-records.index',
            'module' => $module,
            'permission' => 'waste-records manage'
        ]);
    }
}
