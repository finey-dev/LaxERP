<?php

namespace Workdo\Internalknowledge\Listeners;

use App\Events\GivePermissionToRole;
use Workdo\Internalknowledge\Entities\InternalknowledgeUtility;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class GiveRoleToPermission
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(GivePermissionToRole $event)
    {
        $role_id = $event->role_id;
        $rolename = $event->rolename;
        $user_module = $event->user_module;
        if(!empty($user_module))
        {
            if (in_array("Internalknowledge", $user_module))
            {
                InternalknowledgeUtility::GivePermissionToRoles($role_id,$rolename);
            }
        }
    }
}
