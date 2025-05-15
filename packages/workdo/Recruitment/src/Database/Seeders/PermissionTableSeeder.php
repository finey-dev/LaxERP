<?php

namespace Workdo\Recruitment\Database\Seeders;

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
        $module = 'Recruitment';

        $permissions  = [
            'recruitment manage',
            'recruitment dashboard manage',
            'job manage',
            'job create',
            'job edit',
            'job delete',
            'job show',
            'jobcategory manage',
            'jobcategory create',
            'jobcategory edit',
            'jobcategory delete',
            'jobstage manage',
            'jobstage create',
            'jobstage edit',
            'jobstage delete',
            'jobapplication manage',
            'jobapplication create',
            'jobapplication show',
            'jobapplication delete',
            'jobapplication move',
            'jobapplication add skill',
            'jobapplication add note',
            'jobapplication delete note',
            'jobapplication archived manage',
            'jobapplication candidate manage',
            'jobapplication candidate create',
            'jobapplication candidate edit',
            'jobapplication candidate delete',
            'jobonboard manage',
            'jobonboard create',
            'jobonboard edit',
            'jobonboard delete',
            'jobonboard convert',
            'custom question manage',
            'custom question create',
            'custom question edit',
            'custom question delete',
            'interview schedule manage',
            'interview schedule create',
            'interview schedule edit',
            'interview schedule delete',
            'interview schedule show',
            'career manage',
            'letter offer manage',
            'job candidate manage',
            'job candidate create',
            'job candidate edit',
            'job candidate delete',
            'job experience manage',
            'job experience create',
            'job experience edit',
            'job experience show',
            'job experience delete',
            'job project manage',
            'job project create',
            'job project show',
            'job project edit',
            'job project delete',
            'experience candidate job manage',
            'experience candidate job create',
            'experience candidate job show',
            'experience candidate job edit',
            'experience candidate job delete',
            'job qualification manage',
            'job qualification create',
            'job qualification show',
            'job qualification edit',
            'job qualification delete',
            'job skill manage',
            'job skill create',
            'job skill show',
            'job skill edit',
            'job skill delete',
            'job award manage',
            'job award create',
            'job award show',
            'job award edit',
            'job award delete',
            'job post',
            'job attachment manage',
            'job attachment upload',
            'job attachment delete',
            'job note manage',
            'job note create',
            'job note edit',
            'job note show',
            'job note delete',
            'job todo manage',
            'job todo create',
            'job todo edit',
            'job todo show',
            'job todo delete',
            'job activity manage',
            'job activity delete',
            'jobapplication attachment manage',
            'jobapplication attachment upload',
            'jobapplication attachment delete',
            'jobapplication note manage',
            'jobapplication note create',
            'jobapplication note edit',
            'jobapplication note show',
            'jobapplication note delete',
            'jobapplication todo manage',
            'jobapplication todo create',
            'jobapplication todo edit',
            'jobapplication todo show',
            'jobapplication todo delete',
            'jobapplication activity manage',
            'jobapplication activity delete',
            'jobcandidate-category manage',
            'jobcandidate-category create',
            'jobcandidate-category edit',
            'jobcandidate-category delete',
            'jobcandidate-referral manage',
            'jobcandidate-referral create',
            'jobcandidate-referral show',
            'jobcandidate-referral edit',
            'jobcandidate-referral delete',
            'jobcandidate-attachment manage',
            'jobcandidate-attachment upload',
            'jobcandidate-attachment delete',
            'jobcandidate-note manage',
            'jobcandidate-note create',
            'jobcandidate-note edit',
            'jobcandidate-note show',
            'jobcandidate-note delete',
            'jobcandidate-todo manage',
            'jobcandidate-todo create',
            'jobcandidate-todo edit',
            'jobcandidate-todo show',
            'jobcandidate-todo delete',
            'jobcandidate-activity manage',
            'jobcandidate-activity delete',
            'screening type manage',
            'screening type create',
            'screening type edit',
            'screening type delete',
            'screen indicator manage',
            'screen indicator create',
            'screen indicator edit',
            'screen indicator delete',
            'job template manage',
            'job template show',
            'job template edit',
            'job template delete',
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
