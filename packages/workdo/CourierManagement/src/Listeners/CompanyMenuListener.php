<?php

namespace Workdo\CourierManagement\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'CourierManagement';
        $menu = $event->menu;
        $menu->add([
            'category' => 'General',
            'title' => __('Courier Management Dashboard'),
            'icon' => '',
            'name' => 'couriermanagement-dashboard',
            'parent' => 'dashboard',
            'order' => 300,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'dashboard.courier.management',
            'module' => $module,
            'permission' => 'couriermanagement dashboard manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => 'Courier',
            'icon' => 'package',
            'name' => 'couriermanagement',
            'parent' => null,
            'order' => 683,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'courier manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Pending Courier'),
            'icon' => '',
            'name' => 'pendingcourier',
            'parent' => 'couriermanagement',
            'order' => 5,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'get.pending.courier.request',
            'module' => $module,
            'permission' => 'courier pending request manage'
        ]);

        $menu->add([
            'category' => 'Operations',
            'title' => __('Create Courier'),
            'icon' => '',
            'name' => 'createcourier',
            'parent' => 'couriermanagement',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'courier',
            'module' => $module,
            'permission' => 'courier manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Payments'),
            'icon' => '',
            'name' => 'payment',
            'parent' => 'couriermanagement',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'courier.all.payment',
            'module' => $module,
            'permission' => 'courier manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Courier Agents'),
            'icon' => '',
            'name' => 'courier_agents',
            'parent' => 'couriermanagement',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'courier-agents.index',
            'module' => $module,
            'permission' => 'courier agents manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Service Agreements'),
            'icon' => '',
            'name' => 'service_agreements',
            'parent' => 'couriermanagement',
            'order' => 25,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'service-agreements.index',
            'module' => $module,
            'permission' => 'service agreements manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Courier Returns'),
            'icon' => '',
            'name' => 'courier_returns',
            'parent' => 'couriermanagement',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'courier-returns.index',
            'module' => $module,
            'permission' => 'courier returns manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('Contracts'),
            'icon' => '',
            'name' => 'courier_contracts',
            'parent' => 'couriermanagement',
            'order' => 35,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'courier-contracts.index',
            'module' => $module,
            'permission' => 'courier contracts manage'
        ]);
        $menu->add([
            'category' => 'Operations',
            'title' => __('System Setup'),
            'icon' => '',
            'name' => 'systemsetup',
            'parent' => 'couriermanagement',
            'order' => 40,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'courier.branch',
            'module' => $module,
            'permission' => 'courier manage'
        ]);
    }
}
