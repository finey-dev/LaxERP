<?php

namespace Workdo\BudgetPlanner\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'BudgetPlanner';
        $menu = $event->menu;
        $menu->add([
            'category' => 'Finance',
            'title' => 'Budget Planner',
            'icon' => '',
            'name' => 'budgetplanner',
            'parent' => 'accounting',
            'order' => 35,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'budget.index',
            'module' => $module,
            'permission' => 'budget plan manage'
        ]);
    }
}
