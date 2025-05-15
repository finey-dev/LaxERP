<?php

namespace Workdo\Facilities\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Workdo\Facilities\Entities\FacilitiesUtility;
use App\Models\Role;
use App\Models\User;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $super_admin = User::where('type','super admin')->first();
        if(!empty($super_admin))
        {
            $companys = User::where('type','company')->get();
            if(count($companys) > 0)
            {
                foreach ($companys as $key => $company) {
                    $role = Role::where('name','client')->where('created_by',$company->id)->where('guard_name','web')->exists();
                    if(!$role)
                    {
                        $role                   = new Role();
                        $role->name             = 'client';
                        $role->guard_name       = 'web';
                        $role->module           = 'School';
                        $role->created_by       = $company->id;
                        $role->save();
                    }
                }
            }
        }
        if(!empty($super_admin))
        {
            $companys = User::where('type','company')->get();
            if(count($companys) > 0)
            {
                foreach ($companys as $key => $company) {
                    $role = Role::where('name','tenant')->where('created_by',$company->id)->where('guard_name','web')->exists();
                    if(!$role)
                    {
                        $role                   = new Role();
                        $role->name             = 'tenant';
                        $role->guard_name       = 'web';
                        $role->module           = 'School';
                        $role->created_by       = $company->id;
                        $role->save();
                    }
                }
            }
        }
        FacilitiesUtility::GivePermissionToRoles();
    }
}
