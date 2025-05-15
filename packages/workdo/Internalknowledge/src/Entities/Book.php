<?php

namespace Workdo\Internalknowledge\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'description',
        'user_id',
        'created_by',
        'workspace',
    ];
    
    protected static function newFactory()
    {
        return \Workdo\Internalknowledge\Database\factories\BookFactory::new();
    }

}