<?php

namespace Workdo\VisitorManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Visitlog extends Model
{
    use HasFactory;

    protected $table = 'visit_logs';
    protected $fillable = ['visitor_id','check_in','check_out','duration_of_visit','workspace','created_by'];



    public function visitReasons()
    {
        return $this->belongsToMany(VisitReason::class, 'visit_log_reason', 'visit_log_id', 'visit_reason_id')->withPivot('visitor_id');
    }

    public function visitor(){
        return $this->belongsTo(Visitors::class);
    }

    public function assignVisitLogReason($visitReasonId,$visitorId){
        return $this->visitReasons()->sync([$visitReasonId => ['visitor_id' => $visitorId]],false);
    }

    public function getVisitReason(){
        $visitReasons = $this->visitReasons;
        foreach ($visitReasons as $visitReason) {
           return $visitReason->id;
        }
    }
    public function getVisitReasonName(){
        $visitReasons = $this->visitReasons;
        foreach ($visitReasons as $visitReason) {
           return $visitReason->reason;
        }
    }

}
