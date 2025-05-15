<?php

namespace Workdo\TimeTracker\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'TimeTracker';
        $menu = $event->menu;
        $menu->add([
            'category' => 'Productivity',
            'title' => 'Time Tracker',
            'icon' => 'ti ti-alarm',
            'name' => 'timetracker',
            'parent' => null,
            'order' => 1445,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'timetracker.index',
            'module' => $module,
            'permission' => 'timetracker manage'
        ]);
    }
}
