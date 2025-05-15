<?php

namespace Workdo\RepairManagementSystem\Database\Seeders;

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
        $module = 'RepairManagementSystem';

        $permissions  = [
            'repair manage',
            'repair order request manage',
            'repair order request create',
            'repair order request edit',
            'repair order request delete',
            'repair part create',
            'repair part edit',
            'repair part delete',
            'repair movement history show',
            'repair invoice manage',
            'repair invoice create',
            'repair invoice show',
            'repair invoice payment create',
            'repair technician manage',
            'repair technician create',
            'repair technician edit',
            'repair technician delete',
            'warranty manage',
            'warranty create',
            'warranty edit',
            'warranty delete',
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
