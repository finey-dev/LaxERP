<?php

namespace Workdo\MachineRepairManagement\Database\Seeders;

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
        $module = 'MachineRepairManagement';

        $permissions  = [
            'machinerepairmanagement manage',
            'machine dashboard manage',
            'machine manage',
            'machine create',
            'machine edit',
            'machine show',
            'machine delete',
            'repair request manage',
            'repair request create',
            'repair request edit',
            'repair request delete',
            'repair request show',
            'machine diagnosis manage',
            'machine diagnosis create',
            'machine diagnosis edit',
            'machine diagnosis show',
            'machine diagnosis delete',
            'machine invoice payment manage',
            'machine invoice payment create',
            'machine invoice payment edit',
            'machine invoice payment show',
            'machine invoice payment delete',
            'machine repair history manage',
            'machine service agreement manage',
            'machine service agreement create',
            'machine service agreement edit',
            'machine service agreement delete',
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
