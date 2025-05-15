<?php

namespace Workdo\Quotation\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Quotation';
        $menu = $event->menu;
        $menu->add([
            'category' => 'Finance',
            'title' => __('Quotation'),
            'icon' => 'file-check',
            'name' => 'quotation',
            'parent' => null,
            'order' => 260,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'quotation.index',
            'module' => $module,
            'permission' => 'quotation manage'
        ]);
    }
}
