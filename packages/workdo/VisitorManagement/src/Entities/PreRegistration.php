<?php

namespace Workdo\VisitorManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PreRegistration extends Model
{
    use HasFactory;
    protected $table = 'pre_registrations';

    protected $fillable = ['visitor_id','appointment_date','status','workspace','created_by'];

    public function visitor(){
        return $this->belongsTo(Visitors::class);
    }
}
