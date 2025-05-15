<?php

namespace Workdo\Procurement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RfxCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'workspace',
        'created_by',
    ];
    
    protected static function newFactory()
    {
        return \Workdo\Procurement\Database\factories\RfxCategoryFactory::new();
    }
}
