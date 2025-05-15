<?php

namespace Workdo\FileSharing\Entities;

use App\Models\User;
use App\Models\WorkSpace;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileSharingVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'applied_date',
        'action_date',
        'status',
        'attacchment',
        'created_by',
        'workspace',
    ];

    public static $statues = [
        'Pending',
        'Approved',
        'Reject',
    ];

    public function User()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function Workspace()
    {
        return $this->hasOne(WorkSpace::class, 'id', 'workspace');
    }
}
