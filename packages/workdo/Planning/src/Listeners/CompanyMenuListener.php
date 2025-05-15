<?php

namespace Workdo\Planning\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Planning';
        $menu = $event->menu;
        $menu->add([
            'category' => 'Productivity',
            'title' => __('Planning'),
            'icon' => 'ti ti-traffic-cone',
            'name' => 'planning',
            'parent' => null,
            'order' => 1110,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'planning manage'
        ]);
        $menu->add([
            'category' => 'Productivity',
            'title' => __('Challenges'),
            'icon' => '',
            'name' => 'challenges',
            'parent' => 'planning',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'planningchallenges.index',
            'module' => $module,
            'permission' => 'planningchallenges manage'
        ]);
        $menu->add([
            'category' => 'Productivity',
            'title' => __('Charters'),
            'icon' => '',
            'name' => 'new-creativity',
            'parent' => 'planning',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'planningcharters.index',
            'module' => $module,
            'permission' => 'charters manage'
        ]);
        $menu->add([
            'category' => 'Productivity',
            'title' => __('System Setup'),
            'icon' => '',
            'name' => 'system-setup',
            'parent' => 'planning',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'planning-categories.index',
            'module' => $module,
            'permission' => 'planning categories manage'
        ]);
    }
}
