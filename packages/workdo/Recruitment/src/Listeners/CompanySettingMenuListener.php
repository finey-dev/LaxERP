<?php

namespace Workdo\Recruitment\Listeners;

use App\Events\CompanySettingMenuEvent;

class CompanySettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanySettingMenuEvent $event): void
    {
        $module = 'Recruitment';
        $menu = $event->menu;

        $menu->add([
            'title' => __('Recruitment Print Settings'),
            'name' => 'recruitment-print-settings',
            'order' => 170,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'navigation' => 'recruitment-print-settings',
            'module' => $module,
            'permission' => 'recruitment manage'
        ]);
    }
}
