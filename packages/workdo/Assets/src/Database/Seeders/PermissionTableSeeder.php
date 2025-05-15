<?php

namespace Workdo\Assets\Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        Artisan::call('cache:clear');

        $permissions  = [
            'assets manage',
            'assets create',
            'assets edit',
            'assets delete',
            'assets import',
            'assets history manage',
            'assets defective manage',
            'assets defective status',
            'assets system-setup manage',
            'assets category manage',
            'assets category create',
            'assets category edit',
            'assets category delete',
            'assets extra create',
            'assets distribution create',
        ];
     
        $company_role = Role::where('name','company')->first();
        foreach ($permissions as $key => $value)
        {
            $table = Permission::where('name',$value)->where('module','Assets')->exists();
            if(!$table)
            {
                $permission = Permission::create(
                    [
                        'name' => $value,
                        'guard_name' => 'web',
                        'module' => 'Assets',
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
