<?php

namespace Workdo\Procurement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Setting;
use App\Models\User;
use App\Models\WorkSpace;
use App\Models\Permission;
use App\Models\Role;

class Rfx extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'requirement',
        'terms_and_conditions',
        'location',
        'category',
        'skill',
        'position',
        'start_date',
        'end_date',
        'status',
        'applicant',
        'visibility',
        'code',
        'custom_question',
        'billing_type',
        'workspace',
        'created_by',
    ];
    
    protected $table = 'rfxs';
    protected static function newFactory()
    {
        return \Workdo\Procurement\Database\factories\RfxFactory::new();
    }

    public static $status = [
        'active' => 'Active',
        'in_active' => 'In Active',
    ];

    public static $rfx_type = [
        '' => 'Select RFx Type',
        'RFI' => 'RFI',
        'RFQ' => 'RFQ',
        'RFP' => 'RFP',
    ];

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
    public function categories()
    {
        return $this->hasOne(RfxCategory::class, 'id', 'category');
    }
    public function rfxitem()
    {
        return $this->hasOne(RfxCategory::class, 'id', 'rfx_id');
    }

    public function questions()
    {
        $ids = explode(',', $this->custom_question);

        return ProcurementCustomQuestion::whereIn('id', $ids)->get();
    }

    public static function defaultData($company_id = null, $workspace_id = null)
    {
        $rfx_stages = [
            "Applied",
            "Phone Screen",
            "Interview",
            "Hired",
            "Rejected",
        ];
        if ($company_id == Null) {
            $companys = User::where('type', 'company')->get();
            foreach ($companys as $company) {
                $WorkSpaces = WorkSpace::where('created_by', $company->id)->get();
                foreach ($WorkSpaces as $WorkSpace) {

                    foreach ($rfx_stages as $rfx_stage) {
                        $RfxStage = RfxStage::where('title', $rfx_stage)->where('workspace', $WorkSpace->id)->where('created_by', $company->id)->first();
                        if ($RfxStage == null) {
                            $RfxStage = new RfxStage();
                            $RfxStage->title = $rfx_stage;
                            $RfxStage->workspace =  !empty($WorkSpace->id) ? $WorkSpace->id : 0;
                            $RfxStage->created_by = !empty($company->id) ? $company->id : 2;
                            $RfxStage->save();
                        }
                    }
                }
            }
        } elseif ($workspace_id == Null) {
            $company = User::where('type', 'company')->where('id', $company_id)->first();
            $WorkSpaces = WorkSpace::where('created_by', $company->id)->get();
            foreach ($WorkSpaces as $WorkSpace) {
                foreach ($rfx_stages as $rfx_stage) {

                    $RfxStage = RfxStage::where('title', $rfx_stage)->where('workspace', $WorkSpace->id)->where('created_by', $company->id)->first();
                    if ($RfxStage == null) {
                        $RfxStage = new RfxStage();
                        $RfxStage->title = $rfx_stage;
                        $RfxStage->workspace =  !empty($WorkSpace->id) ? $WorkSpace->id : 0;
                        $RfxStage->created_by = !empty($company->id) ? $company->id : 2;
                        $RfxStage->save();
                    }
                }
            }
        } else {
            $company = User::where('type', 'company')->where('id', $company_id)->first();
            $WorkSpace = WorkSpace::where('created_by', $company->id)->where('id', $workspace_id)->first();
            foreach ($rfx_stages as $rfx_stage) {
                $RfxStage = RfxStage::where('title', $rfx_stage)->where('workspace', $WorkSpace->id)->where('created_by', $company->id)->first();
                if ($RfxStage == null) {
                    $RfxStage = new RfxStage();
                    $RfxStage->title = $rfx_stage;
                    $RfxStage->workspace =  !empty($WorkSpace->id) ? $WorkSpace->id : 0;
                    $RfxStage->created_by = !empty($company->id) ? $company->id : 2;
                    $RfxStage->save();
                }
            }
        }

    }

    public static function givePermissionToRoles($role_id = null, $rolename = null)
    {
        $staff_permission = [
            'rfx listing manage',
            'procurement manage',
        ];

        $hr_permission = [
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
            'rfx system-setup manage'
        ];

        if ($role_id == Null) {
            // staff
            $roles_v = Role::where('name', 'staff')->get();

            foreach ($roles_v as $role) {
                foreach ($staff_permission as $permission_v) {
                    $permission = Permission::where('name', $permission_v)->first();
                    if (!empty($permission)) {
                        if (!$role->hasPermission($permission_v)) {
                            $role->givePermission($permission);
                        }
                    }
                }
            }

            // hr
            $roles_v = Role::where('name', 'hr')->get();

            foreach ($roles_v as $role) {
                foreach ($hr_permission as $permission_v) {
                    $permission = Permission::where('name', $permission_v)->first();
                    if (!empty($permission)) {
                        if (!$role->hasPermission($permission_v)) {
                            $role->givePermission($permission);
                        }
                    }
                }
            }
        } else {
            if ($rolename == 'staff') {
                $roles_v = Role::where('name', 'staff')->where('id', $role_id)->first();
                foreach ($staff_permission as $permission_v) {
                    $permission = Permission::where('name', $permission_v)->first();
                    if (!empty($permission)) {
                        if (!$roles_v->hasPermission($permission_v)) {
                            $roles_v->givePermission($permission);
                        }
                    }
                }
            }

            if ($rolename == 'hr') {
                $roles_v = Role::where('name', 'hr')->where('id', $role_id)->first();
                foreach ($hr_permission as $permission_v) {
                    $permission = Permission::where('name', $permission_v)->first();
                    if (!empty($permission)) {
                        if (!$roles_v->hasPermission($permission_v)) {
                            $roles_v->givePermission($permission);
                        }
                    }
                }
            }
        }
    }
}
