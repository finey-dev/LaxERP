<?php

namespace Workdo\Reminder\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Reminder';
        $menu = $event->menu;
        $menu->add([
            'category' =>'Operations',
            'title' => __('Reminder'),
            'icon' => 'bell',
            'name' => 'reminder',
            'parent' => null,
            'order' => 1030,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'reminder.index',
            'module' => $module,
            'permission' => 'reminder manage'
        ]);

    }
}
