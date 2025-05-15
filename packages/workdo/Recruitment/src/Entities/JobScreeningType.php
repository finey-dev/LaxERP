<?php

namespace Workdo\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobScreeningType extends Model
{
    use HasFactory;

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Workdo\Recruitment\Database\factories\JobScreeningTypeFactory::new();
    }

    public function types()
    {
        return $this->hasMany(JobScreenIndicator::class, 'screening_type', 'id');
    }
}
