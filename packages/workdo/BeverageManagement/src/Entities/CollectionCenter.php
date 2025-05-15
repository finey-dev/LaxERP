<?php

namespace Workdo\BeverageManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CollectionCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_name',
        'status',
        'workspace',
        'created_by'
    ];
    
    protected static function newFactory()
    {
        return \Workdo\BeverageManagement\Database\factories\CollectionCenterFactory::new();
    }
}
