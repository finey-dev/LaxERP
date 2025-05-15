<?php

namespace Workdo\Requests\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestFormField extends Model
{
    use HasFactory;

    protected $fillable = ['request_id','name','type','created_by','workspace'];
    protected $table = 'request_form_fields';
    protected static function newFactory()
    {
        return \Workdo\Requests\Database\factories\RequestFormFieldFactory::new();
    }

    public static $type = [
        'Text' => 'Text',
        'Email' => 'Email',
        'Number' => 'Number',
        'Text Area' => 'Text Area',
    ];



}
