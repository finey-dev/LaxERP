<?php

namespace Workdo\VisitorManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitReason extends Model
{
    use HasFactory;

    protected $fillable = ['reason','workspace','created_by'];



    public function visitors(){
        return $this->belongsToMany(Visitors::class,'visit_log_reason');
    }

    public function visitLogs()
    {
        return $this->belongsToMany(VisitLog::class, 'visit_log_reason', 'visit_reason_id', 'visit_log_id');
    }
}
