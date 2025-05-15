<?php

namespace Workdo\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobCandidateReferral extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Workdo\Recruitment\Database\factories\JobCandidateReferralFactory::new();
    }
}
