<?php

namespace Workdo\Facilities\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Permission;
use App\Models\Role;

class FacilitiesUtility extends Model
{
    use HasFactory;

    protected $fillable = [];

    public static function GivePermissionToRoles($role_id = null, $rolename = null)
    {
        $permissions = [
            'facilities dashboard manage',
            'facilities booking manage',
            'facilitiesbooking manage',
            'facilitiesbooking create',
            'facilitiesbooking edit',
            'facilitiesbooking show',
            'facilitiesbooking delete',
            'facilitiesbookingorder move',
            'facilities booking order manage',
            'facilities booking receipt manage',
            'facilities booking receipt show',
        ];

        if ($role_id == Null) {
            $roles_c = Role::where('name', 'client')->get();
            foreach ($roles_c as $role) {
                foreach ($permissions as $permission_s) {
                    $permission = Permission::where('name', $permission_s)->first();
                    if (!$role->hasPermission($permission_s)) {
                        $role->givePermission($permission);
                    }
                }
            }

            $roles_t = Role::where('name', 'tenant')->get();

            foreach ($roles_t as $role) {
                foreach ($permissions as $permission_p) {
                    $permission = Permission::where('name', $permission_p)->first();
                    if (!$role->hasPermission($permission_p)) {
                        $role->givePermission($permission);
                    }
                }
            }

        } else {
            if ($rolename == 'client') {

                $roles_c = Role::where('name', 'client')->where('id', $role_id)->first();
                foreach ($permissions as $permission_s) {
                    $permission = Permission::where('name', $permission_s)->first();
                    if (!$roles_c->hasPermission($permission_s)) {
                        $roles_c->givePermission($permission);
                    }
                }
            } elseif ($rolename == 'tenant') {
                $roles_t = Role::where('name', 'tenant')->where('id', $role_id)->first();
                foreach ($permissions as $permission_p) {
                    $permission = Permission::where('name', $permission_p)->first();
                    if (!$roles_t->hasPermission($permission_p)) {
                        $roles_t->givePermission($permission);
                    }
                }
            }
        }
    }
}
