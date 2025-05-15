<?php

namespace Workdo\Procurement\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Procurement';
        $menu = $event->menu;
        $menu->add([
            'category' => 'General',
            'title' => __('Procurement Dashboard'),
            'icon' => '',
            'name' => 'procurement-dashboard',
            'parent' => 'dashboard',
            'order' => 335,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'procurement.dashboard',
            'module' => $module,
            'permission' => 'procurement dashboard manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Procurement'),
            'icon' => 'shopping-cart-discount',
            'name' => 'procurement',
            'parent' => null,
            'order' => 460,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'procurement manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('RFxs'),
            'icon' => '',
            'name' => 'rfxs',
            'parent' => 'procurement',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'rfx.index',
            'module' => $module,
            'permission' => 'rfx manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('RFx Create'),
            'icon' => '',
            'name' => 'rfx-create',
            'parent' => 'procurement',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'rfx.create',
            'module' => $module,
            'permission' => 'rfx create'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('RFx Application'),
            'icon' => '',
            'name' => 'rfx-application',
            'parent' => 'procurement',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'rfx-application.index',
            'module' => $module,
            'permission' => 'rfxapplication manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('RFx Applicant'),
            'icon' => '',
            'name' => 'rfx-applicant',
            'parent' => 'procurement',
            'order' => 25,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'rfx-applicant.index',
            'module' => $module,
            'permission' => 'rfxapplication applicant manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('RFx Archived'),
            'icon' => '',
            'name' => 'rfx-archived',
            'parent' => 'procurement',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'rfx.application.archived',
            'module' => $module,
            'permission' => 'rfxapplication archived manage'
        ]);
        
        $menu->add([
            'category' => 'HR',
            'title' => __('Vendor On-Boarding'),
            'icon' => '',
            'name' => 'vendor-on-boarding',
            'parent' => 'procurement',
            'order' => 35,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'vendor.on.board',
            'module' => $module,
            'permission' => 'vendoronboard manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('RFx Vendor'),
            'icon' => '',
            'name' => 'rfx vendor',
            'parent' => 'procurement',
            'order' => 40,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'rfx.vendor',
            'module' => $module,
            'permission' => 'rfx vendor view'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Custom Question'),
            'icon' => '',
            'name' => 'custom-question',
            'parent' => 'procurement',
            'order' => 45,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'rfx-custom-question.index',
            'module' => $module,
            'permission' => 'rfx custom question manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Interview Schedule'),
            'icon' => '',
            'name' => 'interview-schedule',
            'parent' => 'procurement',
            'order' => 50,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'rfx-interview-schedule.index',
            'module' => $module,
            'permission' => 'rfx interview schedule manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('System Setup'),
            'icon' => '',
            'name' => 'system-setup',
            'parent' => 'procurement',
            'order' => 55,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'rfx-category.index',
            'module' => $module,
            'permission' => 'rfx system-setup manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('RFxs Listings'),
            'icon' => '',
            'name' => 'rfx-list',
            'parent' => 'procurement',
            'order' => 60,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'rfx-list',
            'module' => $module,
            'permission' => 'rfxlisting manage'
        ]);
    }
}
