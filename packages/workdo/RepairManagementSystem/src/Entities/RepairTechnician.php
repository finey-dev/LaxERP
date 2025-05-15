<?php

namespace Workdo\RepairManagementSystem\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepairTechnician extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'mobile_no','workspace','created_by'];
}
