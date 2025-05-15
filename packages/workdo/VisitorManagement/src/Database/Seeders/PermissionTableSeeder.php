<?php

namespace Workdo\VisitorManagement\Database\Seeders;

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
        $module = 'VisitorManagement';

        $permission  = [
            'visitormanagement manage',
            'visitor manage',
            'visitor create',
            'visitor edit',
            'visitor delete',
            'reason manage',
            'reason create',
            'reason edit',
            'reason delete',
            'visitor log manage',
            'visitor log create',
            'visitor log edit',
            'visitor log delete',
            'visitor timeline manage',
            'visitor reports manage',
            'visitor badge manage',
            'visitor badge create',
            'visitor badge edit',
            'visitor badge delete',
            'visitor pre registration manage',
            'visitor pre registration create',
            'visitor pre registration edit',
            'visitor pre registration delete',
            'visitor documents manage',
            'visitor documents create',
            'visitor documents edit',
            'visitor documents delete',
            'visitor compliance manage',
            'visitor compliance create',
            'visitor compliance edit',
            'visitor compliance delete',
            'visitor incidents manage',
            'visitor incidents create',
            'visitor incidents edit',
            'visitor incidents delete',
            'visitor compliance type manage',
            'visitor compliance type create',
            'visitor compliance type edit',
            'visitor compliance type delete',
            'visitor document type manage',
            'visitor document type create',
            'visitor document type edit',
            'visitor document type delete',
        ];

        $company_role = Role::where('name', 'company')->first();
        foreach ($permission as $key => $value) {
            $table = Permission::where('name', $value)->where('module', $module)->exists();
            if (!$table) {
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
                if (!$company_role->hasPermission($value)) {
                    $company_role->givePermission($permission);
                }
            }
        }
    }
}
