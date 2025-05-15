<?php

namespace Workdo\FixEquipment\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'FixEquipment';
        $menu = $event->menu;
        $menu->add([
            'category' => 'General',
            'title' => 'Fix Equipment',
            'icon' => 'home',
            'name' => 'fixequipment-dashboard',
            'parent' => 'dashboard',
            'order' => 130,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'fix.equipment.dashboard',
            'module' => $module,
            'permission' => 'fix equipment dashboard manage'
        ]);
        $menu->add([
            'category' => 'Finance',
            'title' => __('Fix Equipment'),
            'icon' => 'ti ti-archive',
            'name' => 'fixequipment',
            'parent' => null,
            'order' => 900,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'fix equipment assets manage'
        ]);
        $menu->add([
            'category' => 'Finance',
            'title' => 'Assets',
            'icon' => '',
            'name' => 'fix-assets',
            'parent' => 'fixequipment',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'fix.equipment.assets.index',
            'module' => $module,
            'permission' => 'fix equipment assets manage'
        ]);
        $menu->add([
            'category' => 'Finance',
            'title' => 'Licenses',
            'icon' => '',
            'name' => 'licenses',
            'parent' => 'fixequipment',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'fix.equipment.licence.index',
            'module' => $module,
            'permission' => 'asset licenses manage'
        ]);
        $menu->add([
            'category' => 'Finance',
            'title' => 'Accessories',
            'icon' => '',
            'name' => 'accessories',
            'parent' => 'fixequipment',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'fix.equipment.accessories.index',
            'module' => $module,
            'permission' => 'accessories manage'
        ]);
        $menu->add([
            'category' => 'Finance',
            'title' => 'Consumables',
            'icon' => '',
            'name' => 'consumables',
            'parent' => 'fixequipment',
            'order' => 40,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'fix.equipment.consumables.index',
            'module' => $module,
            'permission' => 'consumables manage'
        ]);
        $menu->add([
            'category' => 'Finance',
            'title' => 'Components',
            'icon' => '',
            'name' => 'components',
            'parent' => 'fixequipment',
            'order' => 50,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'fix.equipment.component.index',
            'module' => $module,
            'permission' => 'equipment components manage'
        ]);
        $menu->add([
            'category' => 'Finance',
            'title' => 'Pre Defined Kit',
            'icon' => '',
            'name' => 'predefined-kit',
            'parent' => 'fixequipment',
            'order' => 60,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'fix.equipment.pre.definded.kit.index',
            'module' => $module,
            'permission' => 'predefined kit manage'
        ]);
        $menu->add([
            'category' => 'Finance',
            'title' => 'Maintenance',
            'icon' => '',
            'name' => 'maintenance',
            'parent' => 'fixequipment',
            'order' => 70,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'fix.equipment.maintenance.index',
            'module' => $module,
            'permission' => 'equipment maintenance manage'
        ]);
        $menu->add([
            'category' => 'Finance',
            'title' => 'Audit',
            'icon' => '',
            'name' => 'audit',
            'parent' => 'fixequipment',
            'order' => 80,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'fix.equipment.audit.index',
            'module' => $module,
            'permission' => 'equipment audit manage'
        ]);
        $menu->add([
            'category' => 'Finance',
            'title' => 'System Setup',
            'icon' => '',
            'name' => 'system-setup',
            'parent' => 'fixequipment',
            'order' => 90,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'fix.equipment.location.index',
            'module' => $module,
            'permission' => 'fix equipment system setup'
        ]);
    }
}
