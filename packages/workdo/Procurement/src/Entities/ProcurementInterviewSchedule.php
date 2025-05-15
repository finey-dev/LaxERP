<?php

namespace Workdo\Procurement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProcurementInterviewSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant',
        'employee',
        'date',
        'time',
        'comment',
        'employee_response',
        'workspace',
        'created_by',
    ];
    
    
    protected static function newFactory()
    {
        return \Workdo\Procurement\Database\factories\ProcurementInterviewScheduleFactory::new();
    }

    public function applications()
    {
       return $this->hasOne(RfxApplication::class,'id','applicant');
    }

   
    
}
