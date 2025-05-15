<?php
namespace Workdo\Reminder\Listeners;
use App\Events\SuperAdminSettingEvent;

class SuperAdminSettingListener
{
    /**
     * Handle the event.
     */
    public function handle(SuperAdminSettingEvent $event): void
    {

        $module = 'Reminder';
        $methodName = 'index';
        $controllerClass = "Workdo\\Reminder\\Http\\Controllers\\SuperAdmin\\SettingsController";
        if (class_exists($controllerClass)) {
            $controller = \App::make($controllerClass);
            if (method_exists($controller, $methodName)) {
                $html = $event->html;
                $settings = $html->getSettings();
                $output =  $controller->{$methodName}($settings);
                $html->add([
                    'html' => $output->toHtml(),
                    'order' => 118,
                    'module' => $module,
                    'permission' => 'reminder manage'
                ]);
            }
        }
    }
}
