<?php

namespace Workdo\Requests\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestConvertData extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'response_data',
        'workspace',
        'created_by'
    ];
    protected static function newFactory()
    {
        return \Workdo\Requests\Database\factories\RequestConvertDataFactory::new();
    }
}
