<?php

namespace Workdo\Planning\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanningChallenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'notes',
        'explantion',
        'end_date',
        'position',
        'workspace',
        'created_by'
    ];

    protected static function newFactory()
    {
        return \Workdo\Planning\Database\factories\PlanningChallengeFactory::new();
    }

    public static $statues = [
        'Ongoing' => 'Ongoing',
        'On Hold' => 'On Hold',
        'Finished' => 'Finished',
    ];
    public function Categories()
    {
        return $this->hasOne(PlanningCategories::class, 'id', 'category');
    }

    public function childs() {
        return $this->hasMany(PlanningChallenge::class,'id','parent_id') ;
    }
}
