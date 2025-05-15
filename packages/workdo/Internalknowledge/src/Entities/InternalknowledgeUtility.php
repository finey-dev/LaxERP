<?php

namespace Workdo\Internalknowledge\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InternalknowledgeUtility extends Model
{
    use HasFactory;

    public static function GivePermissionToRoles($role_id = null,$rolename = null)
    {
        $client_permissions=[
            'internalknowledge manage',
            'book manage',
            'book create',
            'book edit',
            'book delete',
            'book show',
            'article manage',
            'article create',
            'article edit',
            'article delete',
            'article show',
            'article duplicate',
            'my article manage',
        ];

        if($role_id == Null)
        {
            // client
            $roles_c = Role::where('name','client')->get();
            foreach($roles_c as $role)
            {
                foreach($client_permissions as $permission_c){
                    $permission = Permission::where('name',$permission_c)->first();
                    if(!$role->hasPermission($permission_c))
                    {
                        $role->givePermission($permission);
                    }

                }
            }

        }
        else
        {
            if($rolename == 'client')
            {
                $roles_c = Role::where('name','client')->where('id',$role_id)->first();
                foreach($client_permissions as $permission_c){
                    $permission = Permission::where('name',$permission_c)->first();
                    if(!$roles_c->hasPermission($permission_c))
                    {
                        $roles_c->givePermission($permission);
                    }
                }
            }
        }

    }
}
