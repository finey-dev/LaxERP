<?php

namespace Workdo\ManufactureManagement\Listeners;

use App\Events\CompanySettingMenuEvent;

class CompanySettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanySettingMenuEvent $event): void
    {
        $module = 'ManufactureManagement';
        $menu = $event->menu;
        $menu->add([
            'title' => 'ManufactureManagement',
            'name' => 'manufacturemanagement',
            'order' => 100,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'home',
            'navigation' => 'sidenav',
            'module' => $module,
            'permission' => 'manage-dashboard'
        ]);
    }
}
