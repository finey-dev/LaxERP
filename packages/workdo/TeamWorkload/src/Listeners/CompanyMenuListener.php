<?php

namespace Workdo\TeamWorkload\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'TeamWorkload';
        $menu = $event->menu;
        $menu->add([
            'category' => 'Productivity',
            'title' => 'Team Workload',
            'icon' => 'calendar',
            'name' => 'teamworkload',
            'parent' => null,
            'order' => 1360,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'workload manage'
        ]);

        $menu->add([
            'category' => 'Productivity',
            'title' => __('Overview'),
            'icon' => '',
            'name' => 'overview',
            'parent' => 'teamworkload',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'workload.index',
            'module' => $module,
            'permission' => 'workload overview manage'
        ]);

        $menu->add([
            'category' => 'Productivity',
            'title' => __('Settings'),
            'icon' => '',
            'name' => 'workload-settings',
            'parent' => 'teamworkload',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'workload settings manage'
        ]);


        $menu->add([
            'category' => 'Productivity',
            'title' => __('Staff  Shifting'),
            'icon' => '',
            'name' => 'staff-shifting',
            'parent' => 'workload-settings',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'staff-setting.index',
            'module' => $module,
            'permission' => 'staff settings manage'
        ]);

        if(!in_array('Hrm',$event->menu->modules))
        {
            $menu->add([
                'category' => 'Productivity',
                'title' => __('Holidays'),
                'icon' => '',
                'name' => 'holidays-setting',
                'parent' => 'workload-settings',
                'order' => 20,
                'ignore_if' => [],
                'depend_on' => [],
                'route' => 'holidays.index',
                'module' => $module,
                'permission' => 'workload holidays manage'
            ]);
        }

        if(!in_array('Timesheet',$event->menu->modules))
        {
            $menu->add([
                'category' => 'Productivity',
                'title' => __('Workload Timesheet'),
                'icon' => '',
                'name' => 'workload-timesheet',
                'parent' => 'workload-settings',
                'order' => 30,
                'ignore_if' => [],
                'depend_on' => [],
                'route' => 'workload-timesheet.index',
                'module' => $module,
                'disable_module' => 'Timesheet',
                'permission' => 'staff settings manage'
            ]);
        }
    }
}
