<?php

namespace Workdo\DoubleEntry\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\Account\Entities\ChartOfAccount;

class JournalItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal',
        'account',
        'description',
        'debit',
        'credit',
        'workspace',
        'created_by',
    ];

    public function accounts()
    {
        return $this->hasOne(ChartOfAccount::class, 'id', 'account');
    }
}
