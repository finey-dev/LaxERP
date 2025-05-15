<?php

namespace Workdo\CourierManagement\Database\Seeders;

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
        $module = 'CourierManagement';

        $permissions  = [
            'couriermanagement manage',
            'couriermanagement dashboard manage',
            'courier manage',
            'courier create',
            'courier edit',
            'courier delete',
            'courier branch manage',
            'courier branch show',
            'courier branch create',
            'courier branch edit',
            'courier branch delete',
            'servicetype manage',
            'servicetype create',
            'servicetype edit',
            'servicetype delete',
            'tracking manage',
            'tracking create',
            'tracking edit',
            'tracking delete',
            'package category manage',
            'package category create',
            'package category edit',
            'package category delete',
            'courier payment',
            'courier pending request manage',
            'courier pending request create',
            'courier pending request edit',
            'courier pending request delete',
            'courier pending request approve',
            'courier pending request reject',
            'courier agents manage',
            'courier agents create',
            'courier agents edit',
            'courier agents delete',
            'courier agents show',
            'service agreements manage',
            'service agreements create',
            'service agreements edit',
            'service agreements delete',
            'service agreements show',
            'courier contracts manage',
            'courier contracts create',
            'courier contracts edit',
            'courier contracts delete',
            'courier contracts show',
            'courier returns manage',
            'courier returns create',
            'courier returns edit',
            'courier returns delete',
            'courier returns show'
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
