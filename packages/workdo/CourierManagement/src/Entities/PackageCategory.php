<?php

namespace Workdo\CourierManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PackageCategory extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Workdo\CourierManagement\Database\factories\PackageCategoryFactory::new();
    }
}
