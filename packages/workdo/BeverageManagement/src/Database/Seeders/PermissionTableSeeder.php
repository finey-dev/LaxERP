<?php

namespace Workdo\BeverageManagement\Database\Seeders;

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
        $module = 'BeverageManagement';

        $permissions  = [
            'beverage dashboard manage',
            'collection center manage',
            'collection center create',
            'collection center edit',
            'collection center show',
            'collection center delete',
            'raw material manage',
            'raw material create',
            'raw material edit',
            'raw material show',
            'raw material delete',
            'bill of material manage',
            'bill of material create',
            'bill of material edit',
            'bill of material show',
            'bill of material delete',
            'manufacturing manage',
            'manufacturing create',
            'manufacturing edit',
            'manufacturing show',
            'manufacturing delete',
            'manufacturing status',
            'packaging manage',
            'packaging create',
            'packaging edit',
            'packaging show',
            'packaging delete',
            'packaging status',
            'move stock',
            'add stock',
            'sidebar qualitycontrol manage',

            'quality-standards manage',
            'quality-standards create',
            'quality-standards edit',
            'quality-standards delete',
            'quality-standards show',

            'quality-checks manage',
            'quality-checks create',
            'quality-checks edit',
            'quality-checks delete',
            'quality-checks show',

            'beverage-maintenance manage',
            'beverage-maintenance create',
            'beverage-maintenance edit',
            'beverage-maintenance delete',

            'waste-records manage',
            'waste-records create',
            'waste-records edit',
            'waste-records delete',
            'waste-records show',


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
