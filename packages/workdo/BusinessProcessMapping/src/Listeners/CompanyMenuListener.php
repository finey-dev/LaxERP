<?php

namespace Workdo\BusinessProcessMapping\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'BusinessProcessMapping';
        $menu = $event->menu;
        $menu->add([
            'category' => 'Productivity',
            'title' => __('Business Mapping'),
            'icon' => 'circle-dashed',
            'name' => 'businessprocessmapping',
            'parent' => null,
            'order' => 1105,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'business-process-mapping.index',
            'module' => $module,
            'permission' => 'businessprocessmapping manage'
        ]);
    }
}
