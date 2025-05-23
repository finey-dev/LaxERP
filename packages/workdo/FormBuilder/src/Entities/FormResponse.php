<?php

namespace Workdo\FormBuilder\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'response',
    ];

    protected static function newFactory()
    {
        return \Workdo\FormBuilder\Database\factories\FormResponseFactory::new();
    }

}
