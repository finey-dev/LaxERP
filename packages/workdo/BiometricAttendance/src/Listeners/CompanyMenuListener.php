<?php

namespace Workdo\BiometricAttendance\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'BiometricAttendance';
        $menu = $event->menu;
        $menu->add([
            'category' => 'HR',
            'title' => __('Biometric Attendance'),
            'icon' => 'fingerprint',
            'name' => 'biometricattendances',
            'parent' => null,
            'order' => 455,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'biometricattendances manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('Attendance'),
            'icon' => '',
            'name' => 'biometricattendance',
            'parent' => 'biometricattendances',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'biometric-attendance.index',
            'module' => $module,
            'permission' => 'biometricattendance manage'
        ]);
        $menu->add([
            'category' => 'HR',
            'title' => __('System Setup'),
            'icon' => '',
            'name' => 'system-setup',
            'parent' => 'biometricattendances',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'biometric-settings.index',
            'module' => $module,
            'permission' => 'biometricsetting manage'
        ]);
    }
}
