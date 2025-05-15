<?php

namespace Workdo\ManufactureManagement\Listeners;
use App\Events\SuperAdminMenuEvent;

class SuperAdminMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(SuperAdminMenuEvent $event): void
    {
        $module = 'ManufactureManagement';
        $menu = $event->menu;
        $menu->add([
            'title' => 'ManufactureManagement',
            'icon' => 'home',
            'name' => 'manufacturemanagement',
            'parent' => null,
            'order' => 2,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'home',
            'module' => $module,
            'permission' => 'manage-dashboard'
        ]);
    }
}
