<?php

namespace Workdo\Sales\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesDocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent',
        'description',
        'workspace',
        'created_by',
    ];

}
