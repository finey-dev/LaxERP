<?php

namespace Workdo\FileSharing\Database\Seeders;

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
        $module = 'FileSharing';

        $permissions  = [
            'filesharing manage',
            'files manage',
            'files trash-manage',
            'files trash-restore',
            'files trash-delete',
            'files create',
            'files edit',
            'files delete',
            'files download',
            'downloads manage',
            'downloads show',
            'verification manage',
            'verification create',
            'verification edit',
            'verification delete',
            'verification show'
        ];

        $company_role = Role::where('name', 'company')->first();
        $super_admin = Role::where('name', 'super admin')->first();
        foreach ($permissions as $key => $value) {
            $check = Permission::where('name', $value)->where('module', $module)->exists();
            if ($check == false) {
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
                if (!$super_admin->hasPermission($value)) {
                    $super_admin->givePermission($permission);
                }
            }
        }
    }
}
