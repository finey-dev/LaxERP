<?php

namespace Workdo\Requests\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Requests';
        $menu = $event->menu;
        $menu->add([
            'category' => 'Operations',
            'title' => __('Requests'),
            'icon' => 'user-plus',
            'name' => 'request',
            'parent' => null,
            'order' => 888,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'Requests manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Requests'),
            'icon' => 'home',
            'name' => 'requests',
            'parent' => 'request',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'requests.index',
            'module' => 'Base',
            'permission' => 'Requests manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('System Setup'),
            'icon' => 'home',
            'name' => 'requests-system-setup',
            'parent' => 'request',
            'order' =>20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => 'Base',
            'permission' => 'Requests systemsetup manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Category'),
            'icon' => 'requests-category',
            'name' => 'requests-category',
            'parent' => 'requests-system-setup',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'requests-category.index',
            'module' => 'Base',
            'permission' => 'Requests category manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Sub Category'),
            'icon' => 'requests-subcategory',
            'name' => 'requests-subcategory',
            'parent' => 'requests-system-setup',
            'order' => 40,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'requests-subcategory.index',
            'module' => 'Base',
            'permission' => 'Requests subcategory manage'
        ]);
    }
}
