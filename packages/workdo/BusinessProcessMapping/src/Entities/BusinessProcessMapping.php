<?php

namespace Workdo\BusinessProcessMapping\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessProcessMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'description',
        'related_to',
        'related_assign',
        'nodes',
        'connectors',
        'created_by',
        'workspace',

    ];

    protected static function newFactory()
    {
        return \Workdo\BusinessProcessMapping\Database\factories\BusinessProcessMappingFactory::new();
    }

}
