<?php

namespace Workdo\Assets\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetUtility extends Model
{
    use HasFactory;

    protected $fillable = [];

    public static function GivePermissionToRoles($role_id = null,$rolename = null)
    {
        $staff_permissions=[

            'assets manage',
        ];

        if($role_id == Null)
        {
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
            if($rolename == 'staff')
            {
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

    public static function AssetQuantity($assets_id = null,$quantity = null, $purchase_date = null, $type ='Asset')
    {
        $assethistory                  = new AssetHistory();
        $assethistory->assets_id       = $assets_id;
        $assethistory->quantity        = $quantity;
        $assethistory->date            = $purchase_date;
        $assethistory->type            = $type;
        $assethistory->created_by      = creatorId();
        $assethistory->workspace_id    = getActiveWorkSpace();
        if ($assethistory->save()) {
            return true;
        } else {
            return false;
        }


        // return redirect()->route('asset.history.index')->with('success', __('Asset Distribution successfully created.'));
    }
}
