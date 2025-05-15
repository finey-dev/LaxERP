<?php

namespace Workdo\Procurement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class RfxApplicationNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'note_created',
        'note',
        'workspace',
        'created_by',
    ];
    
    protected static function newFactory()
    {
        return \Workdo\Procurement\Database\factories\RfxApplicationNoteFactory::new();
    }
    public function noteCreated()
    {
        return $this->hasOne(User::class, 'id', 'note_created');
    }
}
