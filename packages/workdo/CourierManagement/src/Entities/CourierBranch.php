<?php

namespace Workdo\CourierManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourierBranch extends Model
{
    use HasFactory;
    protected $table = 'courier_branch';

    protected $fillable = [];

    protected static function newFactory()
    {
        return \Workdo\CourierManagement\Database\factories\CourierBranchFactory::new();
    }
}
