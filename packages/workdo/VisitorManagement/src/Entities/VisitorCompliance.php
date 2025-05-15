<?php

namespace Workdo\VisitorManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitorCompliance extends Model
{
    use HasFactory;
    protected $table = 'visitor_compliances';

    protected $fillable = ['visitor_id','compliance_type','status','date','workspace','created_by'];

    public function visitor(){
        return $this->belongsTo(Visitors::class);
    }
    public function compliance(){
        return $this->belongsTo(ComplianceType::class,'compliance_type');
    }

}
