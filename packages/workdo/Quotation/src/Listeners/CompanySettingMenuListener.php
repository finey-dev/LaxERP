<?php

namespace Workdo\Quotation\Listeners;

use App\Events\CompanySettingMenuEvent;

class CompanySettingMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanySettingMenuEvent $event): void
    {
        $module = 'Quotation';
        $menu = $event->menu;
        $menu->add([
            'title' => __('Quotation'),
            'name' => 'quotation',
            'order' => 260,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'home',
            'navigation' => 'quotation-sidenav',
            'module' => $module,
            'permission' => 'quotation manage'
        ]);
    }
}
