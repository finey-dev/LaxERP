<?php

namespace Workdo\Training\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch',
        'firstname',
        'lastname',
        'contact',
        'email',
        'address',
        'expertise',
        'workspace',
        'created_by',
    ];

    protected static function newFactory()
    {
        return \Workdo\Training\Database\factories\TrainerFactory::new();
    }

    public function branches()
    {
        return $this->hasOne(\Workdo\Hrm\Entities\Branch::class, 'id', 'branch');
    }
}
