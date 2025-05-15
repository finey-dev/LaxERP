<?php

namespace Workdo\FileSharing\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'FileSharing';
        $menu = $event->menu;
        $menu->add([
            'category' => 'Productivity',
            'title' => __('File Sharing'),
            'icon' => 'file',
            'name' => 'filesharing',
            'parent' => null,
            'order' => 1200,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'filesharing manage'
        ]);
        $menu->add([
            'category' => 'Productivity',
            'title' => __('Files'),
            'icon' => '',
            'name' => 'Files',
            'parent' => 'filesharing',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'files.index',
            'module' => $module,
            'permission' => 'files manage'
        ]);
        $menu->add([
            'category' => 'Productivity',
            'title' => __('Activity'),
            'icon' => '',
            'name' => 'download',
            'parent' => 'filesharing',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'download-detailes.index',
            'module' => $module,
            'permission' => 'downloads manage'
        ]);
        $menu->add([
            'category' => 'Productivity',
            'title' => __('Trash'),
            'icon' => '',
            'name' => 'trash',
            'parent' => 'filesharing',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'files-trash.index',
            'module' => $module,
            'permission' => 'files trash-manage'
        ]);
        $menu->add([
            'category' => 'Productivity',
            'title' => __('Verification'),
            'icon' => '',
            'name' => 'verification',
            'parent' => 'filesharing',
            'order' => 40,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'file-verification.index',
            'module' => $module,
            'permission' => 'verification manage'
        ]);
    }
}
