<?php

namespace Workdo\MeetingHub\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'MeetingHub';
        $menu = $event->menu;
        $menu->add([
            'category' => 'Communication',
            'title' => __('Meeting Hub'),
            'icon' => 'users',
            'name' => 'meetinghub',
            'parent' => null,
            'order' => 980,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',
            'module' => $module,
            'permission' => 'meetinghub manage'
        ]);
        $menu->add([
            'category' => 'Communication',
            'title' => __('Meeting list'),
            'icon' => '',
            'name' => 'meeting-list',
            'parent' => 'meetinghub',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'meetings.index',
            'module' => $module,
            'permission' => 'meetinghub manage'
        ]);
        $menu->add([
            'category' => 'Communication',
            'title' => __('Meeting Minutes'),
            'icon' => '',
            'name' => 'meeting-minutes',
            'parent' => 'meetinghub',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'meeting-minutes.index',
            'module' => $module,
            'permission' => 'meetinghub manage'
        ]);
        $menu->add([
            'category' => 'Communication',
            'title' => __('Meeting Reports'),
            'icon' => '',
            'name' => 'meeting-report',
            'parent' => 'meetinghub',
            'order' => 30,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'meetinghub.meeting.report',
            'module' => $module,
            'permission' => 'meetinghub report manage'
        ]);
        $menu->add([
            'category' => 'Communication',
            'title' => __('Meeting Type'),
            'icon' => '',
            'name' => 'meeting-type',
            'parent' => 'meetinghub',
            'order' => 40,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'meeting-type.index',
            'module' => $module,
            'permission' => 'meetingtype manage'
        ]);
    }
}
