<?php

namespace Workdo\FormBuilder\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FormBuilderModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'module',
        'submodule',
    ];
    
    protected static function newFactory()
    {
        return \Workdo\FormBuilder\Database\factories\FormBuilderModuleFactory::new();
    }
}
