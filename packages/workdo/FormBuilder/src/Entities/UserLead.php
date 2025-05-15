<?php

namespace Workdo\FormBuilder\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lead_id',
    ];

    protected static function newFactory()
    {
        return \Workdo\FormBuilder\Database\factories\UserLeadFactory::new();
    }
}
