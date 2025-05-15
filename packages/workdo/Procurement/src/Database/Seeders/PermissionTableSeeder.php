<?php

namespace Workdo\Procurement\Database\Seeders;

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
        $module = 'Procurement';

        $permissions = [
            'procurement manage',
            'procurement dashboard manage',
            'rfx manage',
            'rfx create',
            'rfx edit',
            'rfx delete',
            'rfx show',
            'rfxcategory manage',
            'rfxcategory create',
            'rfxcategory edit',
            'rfxcategory delete',
            'rfxstage manage',
            'rfxstage create',
            'rfxstage edit',
            'rfxstage delete',
            'rfxapplication manage',
            'rfxapplication create',
            'rfxapplication show',
            'rfxapplication delete',
            'rfxapplication move',
            'rfxapplication add skill',
            'rfxapplication add note',
            'rfxapplication delete note',
            'rfxapplication archived manage',
            'rfxapplication applicant manage',
            'rfxapplication applicant create',
            'rfxapplication applicant edit',
            'rfxapplication applicant delete',
            'vendoronboard manage',
            'vendoronboard create',
            'vendoronboard edit',
            'vendoronboard delete',
            'vendoronboard convert',
            'rfx custom question manage',
            'rfx custom question create',
            'rfx custom question edit',
            'rfx custom question delete',
            'rfx interview schedule manage',
            'rfx interview schedule create',
            'rfx interview schedule edit',
            'rfx interview schedule delete',
            'rfx interview schedule show',
            'rfxlisting manage',
            'rfx applicant manage',
            'rfx applicant create',
            'rfx applicant edit',
            'rfx applicant delete',
            'budgettype manage',
            'budgettype create',
            'budgettype edit',
            'budgettype delete',
            'rfx vendor view',
            'rfx system-setup manage',
        ];

        $company_role = Role::where('name', 'company')->first();
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
            }
        }
    }
}
