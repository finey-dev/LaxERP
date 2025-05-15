<?php

namespace Workdo\FormBuilder\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'name',
        'type',
        'workspace',
        'created_by',
    ];

    protected static function newFactory()
    {
        return \Workdo\FormBuilder\Database\factories\FormFieldFactory::new();
    }
}
