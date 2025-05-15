<?php

namespace Workdo\VisitorManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitorIncident extends Model
{
    use HasFactory;
    protected $table = 'visitor_incidents';

    protected $fillable = ['visitor_id','incident_date','incident_description','action_taken','workspace','created_by'];

    public function visitor(){
        return $this->belongsTo(Visitors::class);
    }
}
