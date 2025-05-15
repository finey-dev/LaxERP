<?php

namespace Workdo\Reminder\Listeners;

use App\Events\CompanySettingMenuEvent;

class CompanySettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanySettingMenuEvent $event): void
    {
        $module = 'Reminder';
        $menu = $event->menu;
        $menu->add([
            'title' => __('Reminder'),
            'name' => 'reminder',
            'order' => 118,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'home',
            'navigation' => 'sidenav-reminder',
            'module' => $module,
            'permission' => 'reminder manage'
        ]);
    }
}
