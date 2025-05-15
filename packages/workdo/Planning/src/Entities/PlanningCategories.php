<?php

namespace Workdo\Planning\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanningCategories extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'workspace',
        'created_by',
    ];

    protected static function newFactory()
    {
        return \Workdo\Planning\Database\factories\PlanningCategoriesFactory::new();
    }
 
}
