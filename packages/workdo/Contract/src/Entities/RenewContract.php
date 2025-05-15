<?php

namespace Workdo\Contract\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RenewContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'value',
        'start_date',
        'end_date',
    ];
    
   
}
