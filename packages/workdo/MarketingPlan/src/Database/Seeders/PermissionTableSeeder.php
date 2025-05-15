<?php

namespace Workdo\MarketingPlan\Database\Seeders;

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
        $module = 'MarketingPlan';

        $permissions  = [
            'marketing plan manage',
            'marketing plan create',
            'marketing plan edit',
            'marketing plan delete',
            'marketing plan show',
            'marketingplan item create',
            'marketingplan item manage',
            'marketingplan item delete',
            'marketingplan business summary create',
            'marketingplan company description create',
            'marketingplan team create',
            'marketingplan business initiative create',
            'marketingplan target market create',
            'marketingplan marketing channels create',
            'marketingplan budget create',
            'marketingplan notes create',
            'marketingplan comment replay',
            'marketing plan move',
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
