<?php

namespace Workdo\VisitorManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitorBadge extends Model
{
    use HasFactory;
    protected $table = 'visitor_badges';
    protected $fillable = ['visitor_id','badge_number','issue_date','return_date','workspace','created_by'];
    public function visitor(){
        return $this->belongsTo(Visitors::class);
    }
}
