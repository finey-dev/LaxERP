<?php

namespace Workdo\Training\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'workspace',
        'created_by',
    ];
    
    protected static function newFactory()
    {
        return \Workdo\Training\Database\factories\TrainingTypeFactory::new();
    }
}
