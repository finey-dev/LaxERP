<?php

namespace Workdo\CourierManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourierAgents extends Model
{
    use HasFactory;
    protected $table = 'courier_agents';

    protected $guarded = [];
    public function branch()
    {
        return $this->belongsTo(CourierBranch::class, 'branch_id');
    }
}
