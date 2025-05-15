<?php

namespace Workdo\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobScreenIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'screening_type'
    ];
    
    protected static function newFactory()
    {
        return \Workdo\Recruitment\Database\factories\JobScreenIndicatorFactory::new();
    }

    public function screening_name()
    {
        return $this->hasOne(\Workdo\Recruitment\Entities\JobScreeningType::class, 'id', 'screening_type');
    }
}
