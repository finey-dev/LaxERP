<?php

namespace Workdo\FileSharing\Listeners;
use App\Events\SuperAdminMenuEvent;

class SuperAdminMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(SuperAdminMenuEvent $event): void
    {
        $module = 'FileSharing';
        $menu = $event->menu;
        $menu->add([
            'category' => 'Operations',
            'title' => __('File Share Verification'),
            'icon' => 'file',
            'name' => 'fileshareverification',
            'parent' => null,
            'order' => 550,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'file-verification.index',
            'module' => $module,
            'permission' => 'verification manage'
        ]);
    }
}
