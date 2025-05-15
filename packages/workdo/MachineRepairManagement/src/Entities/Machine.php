<?php

namespace Workdo\MachineRepairManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Permission;
use App\Models\Role;

class Machine extends Model
{
    use HasFactory;

    protected $table='machines';
    protected $fillable = [
        'name',
        'manufacturer',
        'model',
        'installation_date',
        'last_maintenance_date',
        'description',
        'status',
        'workspace',
        'created_by',
    ];
    
    public static function GivePermissionToRoles($role_id = null,$rolename = null)
    {
        $staff_permissions=[

        ];
        if($role_id == Null)
        {
            // staff
            $roles_v = Role::where('name','staff')->get();

            foreach($roles_v as $role)
            {
                foreach($staff_permissions as $permission_v){
                    $permission = Permission::where('name',$permission_v)->first();
                    if(!$role->hasPermission($permission_v))
                    {
                        $role->givePermission($permission);
                    }
                }
            }
        }
        else
        {
            if($rolename == 'staff'){
                $roles_v = Role::where('name','staff')->where('id',$role_id)->first();
                foreach($staff_permissions as $permission_v){
                    $permission = Permission::where('name',$permission_v)->first();
                    if(!$roles_v->hasPermission($permission_v))
                    {
                        $roles_v->givePermission($permission);
                    }
                }
            }
        }

    }
}
