<?php

namespace Workdo\CourierManagement\Listeners;

use App\Events\CompanySettingMenuEvent;

class CompanySettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanySettingMenuEvent $event): void
    {
        $module = 'CourierManagement';
        $menu = $event->menu;
        $menu->add([
            'title' => __('Courier Management Setting'),
            'name' => 'couriermanagement',
            'order' => 380,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'navigation' => 'couriermanagement',
            'module' => $module,
            'permission' => 'couriermanagement manage'
        ]);
    }
}
