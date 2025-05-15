<?php

namespace Workdo\MarketingPlan\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'MarketingPlan';
        $menu = $event->menu;
        $menu->add([
            'category' => 'Productivity',
            'title' => __('Marketing Plan'),
            'icon' => '',
            'name' => 'marketing-plan',
            'parent' => 'planning',
            'order' => 22,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'marketing-plan.index',
            'module' => $module,
            'permission' => 'marketing plan manage'
        ]);
    }
}
