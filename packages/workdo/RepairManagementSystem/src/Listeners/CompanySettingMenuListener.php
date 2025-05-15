<?php

namespace Workdo\RepairManagementSystem\Listeners;

use App\Events\CompanySettingMenuEvent;

class CompanySettingMenuListener
{

    public function handle(CompanySettingMenuEvent $event): void
    {
        $module = 'RepairManagementSystem';
        $menu = $event->menu;
        $menu->add([
            'title' => __('Repair Management System Setting'),
            'name' => 'repair-management-system-setting',
            'order' => 350,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'navigation' => 'RepairManagementSystem-sidenav',
            'module' => $module,
            'permission' => 'repair manage'
        ]);
    }
}
