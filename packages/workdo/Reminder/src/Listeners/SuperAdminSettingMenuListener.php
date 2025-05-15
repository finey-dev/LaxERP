<?php

namespace Workdo\Reminder\Listeners;
use App\Events\SuperAdminSettingMenuEvent;

class SuperAdminSettingMenuListener
{
    /**
     * Handle the event.
     */

    public function handle(SuperAdminSettingMenuEvent $event): void
    {
        $module = 'Reminder';
        $menu = $event->menu;
        $menu->add([
            'title' => __('Reminder'),
            'name' => 'reminder',
            'order' => 118,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'navigation' => 'sidenav-reminder',
            'module' => $module,
            'permission' => 'reminder manage'
        ]);
    }



}
