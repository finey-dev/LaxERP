<?php

namespace Workdo\Sales\Database\Seeders;

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
        $module = 'Sales';

        $permissions  = [
            'sales manage',
            'sales dashboard manage',
            'sales setup manage',
            'sales report manage',
            'contact manage',
            'contact create',
            'contact edit',
            'contact delete',
            'contact show',
            'contact import',
            'opportunities manage',
            'opportunities create',
            'opportunities edit',
            'opportunities show',
            'opportunities delete',
            'opportunitiesstage manage',
            'opportunitiesstage create',
            'opportunitiesstage edit',
            'opportunitiesstage delete',
            'salesaccount manage',
            'salesaccount create',
            'salesaccount edit',
            'salesaccount delete',
            'salesaccount show',
            'salesaccount import',
            'salesaccounttype manage',
            'salesaccounttype create',
            'salesaccounttype edit',
            'salesaccounttype delete',
            'accountindustry manage',
            'accountindustry create',
            'accountindustry edit',
            'accountindustry delete',
            'salesdocument manage',
            'salesdocument create',
            'salesdocument edit',
            'salesdocument delete',
            'salesdocument show',
            'salesdocumenttype manage',
            'salesdocumenttype create',
            'salesdocumenttype edit',
            'salesdocumenttype delete',
            'documentfolder manage',
            'documentfolder create',
            'documentfolder edit',
            'documentfolder delete',
            'call manage',
            'call create',
            'call edit',
            'call delete',
            'call show',
            'meeting manage',
            'meeting create',
            'meeting edit',
            'meeting delete',
            'meeting show',
            'stream manage',
            'stream create',
            'stream delete',
            'case manage',
            'case create',
            'case edit',
            'case delete',
            'case show',
            'casetype manage',
            'casetype create',
            'casetype edit',
            'casetype delete',
            'quote manage',
            'quote create',
            'quote edit',
            'quote delete',
            'quote show',
            'quote report',
            'shippingprovider manage',
            'shippingprovider create',
            'shippingprovider edit',
            'shippingprovider delete',
            'salesorder manage',
            'salesorder create',
            'salesorder edit',
            'salesorder delete',
            'salesorder show',
            'salesorder report',
            'salesinvoice manage',
            'salesinvoice create',
            'salesinvoice edit',
            'salesinvoice delete',
            'salesinvoice show',
            'salesinvoice report',
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
