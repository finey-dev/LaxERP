<?php

namespace Workdo\Performance\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Indicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch',
        'designation',
        'customer_experience',
        'marketing',
        'administration',
        'professionalism',
        'integrity',
        'attendance',
        'workspace',
        'created_by',
    ];
    
    public static $technical = [
        'None',
        'Beginner',
        'Intermediate',
        'Advanced',
        'Expert / Leader',
    ];

    public static $organizational = [
        'None',
        'Beginner',
        'Intermediate',
        'Advanced',
    ];
    protected static function newFactory()
    {
        return \Workdo\Performance\Database\factories\IndicatorFactory::new();
    }
    public function branches()
    {
        return $this->hasOne(\Workdo\Hrm\Entities\Branch::class, 'id', 'branch');
    }

    public function departments()
    {
        return $this->hasOne(\Workdo\Hrm\Entities\Department::class, 'id', 'department');
    }

    public function designations()
    {
        return $this->hasOne(\Workdo\Hrm\Entities\Designation::class, 'id', 'designation');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
