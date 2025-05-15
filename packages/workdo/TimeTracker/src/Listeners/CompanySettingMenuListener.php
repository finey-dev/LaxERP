<?php

namespace Workdo\TimeTracker\Listeners;

use App\Events\CompanySettingMenuEvent;

class CompanySettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanySettingMenuEvent $event): void
    {
        $module = 'TimeTracker';
        $menu = $event->menu;
        $menu->add([
            'title' => __('Time Tracker Setting'),
            'name' => 'timetracker-setting',
            'order' => 340,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'navigation' => 'timetracker-setting',
            'module' => $module,
            'permission' => 'timetracker manage'
        ]);
    }
}
