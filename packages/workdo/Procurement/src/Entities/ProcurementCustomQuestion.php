<?php

namespace Workdo\Procurement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProcurementCustomQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'is_required',
        'workspace',
        'created_by',
    ];
    protected static function newFactory()
    {
        return \Workdo\Procurement\Database\factories\ProcurementCustomQuestionFactory::new();
    }
    public static $is_required = [
        'yes' => 'Yes',
        'no' => 'No',
    ];
}
