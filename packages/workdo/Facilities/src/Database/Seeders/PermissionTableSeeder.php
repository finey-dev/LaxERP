<?php

namespace Workdo\Facilities\Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class PermissionTableSeeder extends Seeder
{
     public function run()
    {
        Model::unguard();
        Artisan::call('cache:clear');
        $module = 'Facilities';

        $permissions  = [
            'facilities dashboard manage',
            'facilities booking manage',
            'facilitiesservice manage',
            'facilitiesservice create',
            'facilitiesservice edit',
            'facilitiesservice delete',
            'facilitiesworking manage',
            'facilitiesworking create',
            'facilitiesworking edit',
            'facilitiesworking delete',
            'facilitiesbooking manage',
            'facilitiesbooking create',
            'facilitiesbooking edit',
            'facilitiesbooking show',
            'facilitiesbooking delete',
            'facilities settings manage',
            'facilitiesbookingorder move',
            'facilities booking order manage',
            'facilities booking receipt manage',
            'facilities booking receipt show',
            'facilitiesspace manage',
            'facilitiesspace create',
            'facilitiesspace edit',
            'facilitiesspace delete'
        ];

        $company_role = Role::where('name','company')->first();
        foreach ($permissions as $key => $value)
        {
            $check = Permission::where('name',$value)->where('module',$module)->exists();
            if($check == false)
            {
                $permission = Permission::create(
                    [
                        'name' => $value,
                        'guard_name' => 'web',
                        'module' => $module,
                        'created_by' => 0,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s')
                    ]
                );
                if(!$company_role->hasPermission($value))
                {
                    $company_role->givePermission($permission);
                }
            }
        }
    }
}
