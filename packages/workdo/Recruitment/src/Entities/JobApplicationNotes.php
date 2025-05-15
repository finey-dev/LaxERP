<?php

namespace Workdo\Recruitment\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobApplicationNotes extends Model
{
    use HasFactory;

    protected $fillable = [
        'jobapplication_id',
        'description',
        'workspace',
        'created_by',
    ];
    
    protected $table = 'jobapplication_notes';

    protected static function newFactory()
    {
        return \Workdo\Recruitment\Database\factories\JobApplicationNotesFactory::new();
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
