<?php

namespace Workdo\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobQualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'organization',
        'start_date',
        'end_date',
        'country',
        'state',
        'city',
        'reference',
        'description',
    ];
    
    protected static function newFactory()
    {
        return \Workdo\Recruitment\Database\factories\JobQualificationFactory::new();
    }
}
