<?php

namespace Workdo\Performance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Performance_Type extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'workspace',
        'created_by',
    ];
    
    protected static function newFactory()
    {
        return \Workdo\Performance\Database\factories\PerformanceTypeFactory::new();
    }
    public function types()
    {
        return $this->hasMany(Competencies::class, 'type', 'id');
    }
}
