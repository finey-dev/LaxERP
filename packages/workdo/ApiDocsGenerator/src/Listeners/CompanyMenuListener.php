<?php

namespace Workdo\ApiDocsGenerator\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'ApiDocsGenerator';
        $menu = $event->menu;
        $menu->add([
            'category'=> 'Settings',
            'title' => 'Api Docs',
            'icon' => 'vector-triangle',
            'name' => 'api-docs',
            'parent' => null,
            'order' => 1600,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'api.docs',
            'module' => $module,
            'permission' => 'api manage'
        ]);
    }
}
