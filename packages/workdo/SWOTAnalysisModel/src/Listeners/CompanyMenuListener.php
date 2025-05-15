<?php

namespace Workdo\SWOTAnalysisModel\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'SWOTAnalysisModel';
        $menu = $event->menu;
        $menu->add([
            'category' => 'Productivity',
            'title' => 'SWOT Analysis Model',
            'icon' => 'home',
            'name' => 'swotanalysismodel',
            'parent' => 'planning',
            'order' => 26,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'swotanalysis-model.index',
            'module' => $module,
            'permission' => 'SWOTAnalysisModel manage'
        ]);
    }
}
