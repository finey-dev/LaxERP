<?php

namespace Workdo\Contract\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContractComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'user_id',
        'comment',
        'workspace',
    ];

   
}
