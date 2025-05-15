<?php

namespace Workdo\Planning\Database\Seeders;

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
        $module = 'Planning';

        $permissions  = [
            'planning manage',
            'planningchallenges manage',
            'planningchallenges create',
            'planningchallenges edit',
            'planningchallenges delete',
            'planningchallenges show',
            'charters manage',
            'charters create',
            'charters edit',
            'charters delete',
            'charters show',
            'charters move',
            'planning categories manage',
            'planning categories create',
            'planning categories edit',
            'planning categories delete',
            'planning stage manage',
            'planning stage create',
            'planning stage edit',
            'planning stage delete',
            'planning status manage',
            'planning status create',
            'planning status edit',
            'planning status delete',
            'charters organisational effects create',
            'charters goal description create',
            'charters notes create',
            'charters comment replay',
            'charters decription create',


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
