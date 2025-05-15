<?php

namespace Workdo\VisitorManagement\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'VisitorManagement';
        $menu = $event->menu;
        $menu->add([
            'category'=>'Productivity',
            'title' => 'Visitors',
            'icon' => 'man',
            'name' => 'visitors',
            'parent' => null,
            'order' => 1510,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'visitor manage'
        ]);
        $menu->add([
            'category'=>'Productivity',
            'title' => __('Visitors'),
            'icon' => '',
            'name' => 'visitors-detail',
            'parent' => 'visitors',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'visitors.index',
            'module' => $module

        ]);
        $menu->add([
            'category'=>'Productivity',
            'title' => __('Visitor Log'),
            'icon' => '',
            'name' => 'visitor-log',
            'parent' => 'visitors',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'visitor-log.index',
            'module' => $module,
            'permission' => 'visitor log manage'
        ]);
        
        $menu->add([
            'category'=>'Productivity',
            'title' => __('Visitor Timeline'),
            'icon' => '',
            'name' => 'visitor-timeline',
            'parent' => 'visitors',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'visitors.timeline',
            'module' => $module,
            'permission' => 'visitor timeline manage'
        ]);

        $menu->add([
            'category'=>'Productivity',
            'title' => __('Visitor Badge'),
            'icon' => '',
            'name' => 'visitor-badge',
            'parent' => 'visitors',
            'order' => 40,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'visitors-badge.index',
            'module' => $module,
            'permission' => 'visitor badge manage'
        ]);
        $menu->add([
            'category'=>'Productivity',
            'title' => __('Pre Registration'),
            'icon' => '',
            'name' => 'pre-registration',
            'parent' => 'visitors',
            'order' => 50,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'visitors-pre-registration.index',
            'module' => $module,
            'permission' => 'visitor pre registration manage'
        ]);

        $menu->add([
            'category'=>'Productivity',
            'title' => __('Visitor Documents'),
            'icon' => '',
            'name' => 'visitor-documents',
            'parent' => 'visitors',
            'order' => 60,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'visitors-documents.index',
            'module' => $module,
            'permission' => 'visitor documents manage'
        ]);
        $menu->add([
            'category'=>'Productivity',
            'title' => __('Visitor Compliance'),
            'icon' => '',
            'name' => 'visitor-compliance',
            'parent' => 'visitors',
            'order' => 70,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'visitors-compliance.index',
            'module' => $module,
            'permission' => 'visitor compliance manage'
        ]);
        $menu->add([
            'category'=>'Productivity',
            'title' => __('Visitor Incidents'),
            'icon' => '',
            'name' => 'visitor-incidents',
            'parent' => 'visitors',
            'order' => 75,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'visitors-incidents.index',
            'module' => $module,
            'permission' => 'visitor incidents manage'
        ]);
        $menu->add([
            'category'=>'Productivity',
            'title' => __('Visitor Reports'),
            'icon' => '',
            'name' => 'visitor-reports',
            'parent' => 'visitors',
            'order' => 80,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'visitor.reports.index',
            'module' => $module,
            'permission' => 'visitor reports manage'
        ]);
        $menu->add([
            'category'=>'Productivity',
            'title' => __('System Setup'),
            'icon' => '',
            'name' => 'system-setup',
            'parent' => 'visitors',
            'order' => 85,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'visitors-compliance-type.index',
            'module' => $module,
            'permission' => 'visitor compliance type manage'
        ]);
    }
}
