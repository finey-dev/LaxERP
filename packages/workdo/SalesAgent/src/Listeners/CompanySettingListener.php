<?php

namespace Workdo\SalesAgent\Listeners;

use App\Events\CompanySettingEvent;

class CompanySettingListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanySettingEvent $event): void
    {
        $module = 'SalesAgent';
        $methodName = 'index';
        $controllerClass = "Workdo\\SalesAgent\\Http\\Controllers\\Company\\SettingsController";
        if (class_exists($controllerClass)) {
            $controller = \App::make($controllerClass);
            if (method_exists($controller, $methodName)) {
                $html = $event->html;
                $settings = $html->getSettings();
                $output =  $controller->{$methodName}($settings);
                $html->add([
                    'html' => $output->toHtml(),
                    'order' => 615,
                    'module' => $module,
                    'permission' => 'salesagent manage'
                ]);
            }
        }
    }
}
