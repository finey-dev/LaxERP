<?php

namespace Workdo\FixEquipment\Database\Seeders;

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

        $permission = [
            'fixequipment manage',
            'fix equipment dashboard manage',
            'fix equipment assets manage',
            'fix equipment assets create',
            'fix equipment assets edit',
            'fix equipment assets delete',
            'asset licenses manage',
            'asset licenses create',
            'asset licenses edit',
            'asset licenses delete',
            'accessories manage',
            'accessories create',
            'accessories edit',
            'accessories delete',
            'consumables manage',
            'consumables create',
            'consumables edit',
            'consumables delete',
            'equipment components manage',
            'equipment components create',
            'equipment components edit',
            'equipment components delete',
            'predefined kit manage',
            'predefined kit create',
            'predefined kit edit',
            'predefined kit delete',
            'equipment maintenance manage',
            'equipment maintenance create',
            'equipment maintenance edit',
            'equipment maintenance delete',
            'equipment audit manage',
            'equipment audit create',
            'equipment audit edit',
            'equipment audit delete',
            'equipment location manage',
            'equipment location create',
            'equipment location edit',
            'equipment location delete',
            'depreciation manage',
            'depreciation create',
            'depreciation edit',
            'depreciation delete',
            'asset manufacturers manage',
            'asset manufacturers create',
            'asset manufacturers edit',
            'asset manufacturers delete',
            'equipment categories manage',
            'equipment categories create',
            'equipment categories edit',
            'equipment categories delete',
            'equipment status labels manage',
            'equipment status labels create',
            'equipment status labels edit',
            'equipment status labels delete',
            'fix equipment system setup',
        ];

        $company_role = Role::where('name','company')->first();

        foreach ($permission as $key => $value)
        {
            $table = Permission::where('name',$value)->where('module','FixEquipment')->exists();
            if(!$table)
            {
                $permission = Permission::create(
                    [
                        'name' => $value,
                        'guard_name' => 'web',
                        'module' => 'FixEquipment',
                        'created_by' => 0,
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s')
                        ]
                    );

                if(!$company_role->hasPermission($value)){
                    $company_role->givePermission($permission);
                }
            }
        }

    }
}
