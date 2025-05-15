<?php

namespace Workdo\MachineRepairManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class MachineRepairRequest extends Model
{
    use HasFactory;

    protected $table='machine_repair_requests';
    protected $fillable = [
        'machine_id',
        'customer_name',
        'customer_email',
        'staff_id',
        'description_of_issue',
        'date_of_request',
        'priority_level',
        'status',
        'workspace',
        'created_by',
    ];

    public function machine()
    {
        return $this->hasOne(Machine::class, 'id', 'machine_id');
    }
    public function customer()
    {
        return $this->hasOne(User::class, 'id', 'customer_id');
    }
    public function staff()
    {
        return $this->hasOne(User::class, 'id', 'staff_id');
    }

    public static function machineRepairNumberFormat($number,$company_id = null,$workspace = null)
    {
        if(!empty($company_id) && empty($workspace))
        {
            $company_settings = getCompanyAllSetting($company_id);
        }
        elseif(!empty($company_id) && !empty($workspace))
        {
            $company_settings = getCompanyAllSetting($company_id,$workspace);
        }
        else
        {
            $company_settings = getCompanyAllSetting();
        }
        $data = !empty($company_settings['machine_repair_prefix']) ? $company_settings['machine_repair_prefix'] : '#MRR';

        return $data. sprintf("%05d", $number);
    }
}
