<?php

namespace Workdo\FormBuilder\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'FormBuilder';
        $menu = $event->menu;
        $menu->add([
            'category' => 'Sales',
            'title' => __('Form Builder'),
            'icon' => 'ti ti-file-code',
            'name' => 'formbuilder',
            'parent' => '',
            'order' => 510,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'form_builder.index',
            'module' => $module,
            'permission' => 'formbuilder manage'
        ]);
    }
}
