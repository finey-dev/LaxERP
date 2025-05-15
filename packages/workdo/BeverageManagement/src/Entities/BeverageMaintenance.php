<?php

namespace Workdo\BeverageManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BeverageMaintenance extends Model
{
    use HasFactory;
    protected $table = 'beverage_maintenances';

    protected $guarded = [];
}
