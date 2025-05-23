<?php

namespace Workdo\SWOTAnalysisModel\Database\Seeders;

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
        $module = 'SWOTAnalysisModel';

        $permissions  = [
            'SWOTAnalysisModel manage',
            'SWOTAnalysisModel create',
            'SWOTAnalysisModel edit',
            'SWOTAnalysisModel delete',
            'SWOTAnalysisModel show',
            'SWOTAnalysisModel Move',
            'SWOTAnalysisModel Strengths create',
            'SWOTAnalysisModel Weaknesses create',
            'SWOTAnalysisModel Opportunities create',
            'SWOTAnalysisModel Threats create',
            'SWOTAnalysisModel notes create',
            'SWOTAnalysisModel comment replay',
            'SWOTAnalysisModel decription create',

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
