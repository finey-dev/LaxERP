<?php

namespace Workdo\Requests\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestResponse extends Model
{
    use HasFactory;

    protected $fillable = ['request_id', 'response'];

    protected static function newFactory()
    {
        return \Workdo\Requests\Database\factories\RequestResponseFactory::new();
    }
}
