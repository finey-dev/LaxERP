<?php

namespace Workdo\Requests\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name','workspace','created_by'];

    protected static function newFactory()
    {
        return \Workdo\Requests\Database\factories\RequestCategoryFactory::new();
    }
}
