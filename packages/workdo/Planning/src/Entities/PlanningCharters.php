<?php

namespace Workdo\Planning\Entities;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanningCharters extends Model
{
    use HasFactory;

    protected $fillable = [
        'charter_name',
        'dsescription',
        'status',
        'stage',
        'challenge',
        'visibility_type',
        'rating',
        'thumbnail_image',
        'video_file',
        'user_id',
        'role_id',
        'order',
        'charter_attachments',
        'organisational_effects',
        'goal_description',
        'notes',
        'workspace',
        'created_by',
    ];

    protected static function newFactory()
    {
        return \Workdo\Planning\Database\factories\PlanningChartersFactory::new();
    }

    public function stages()
    {
        return $this->hasOne(PlanningStage::class, 'id', 'stage');
    }
    public function statuses()
    {
        return $this->hasOne(PlanningStatus::class, 'id', 'status');
    }

    public function challenges()
    {
        return $this->hasOne(PlanningChallenge::class, 'id', 'challenge');
    }

    public function roles()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function countCharterComments()
    {
        return PlanningComment::where('charter_id', '=', $this->id)->count();
    }

    public function countAttachment()
    {
        $attachments = json_decode($this->charter_attachments, true);
        return is_array($attachments) ? count($attachments) : 0;
    }

    public function type()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
