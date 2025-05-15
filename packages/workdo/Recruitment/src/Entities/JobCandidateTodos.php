<?php

namespace Workdo\Recruitment\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobCandidateTodos extends Model
{
    use HasFactory;

    protected $fillable = [
        'jobcandidate_id',
        'title',
        'assigned_to',
        'description',
        'status',
        'priority',
        'start_date',
        'assign_by',
        'due_date',
        'module',
        'sub_module',
        'workspace_id',
        'created_by',
    ];
    
    protected $table = 'todos';

    protected static function newFactory()
    {
        return \Workdo\Recruitment\Database\factories\JobCandidateTodosFactory::new();
    }

    public function assignedByUser()
    {
        return $this->belongsTo(User::class, 'assign_by');
    }

    public function users()
    {
        return User::whereIn('id',explode(',',$this->assigned_to))->get();
    }

    public static function getUsersData()
    {
        $zoommeetings = \DB::table('job_todos')->get();

        $employeeIds = [];
        foreach ($zoommeetings as $item) {
            $employees = explode(',', $item->assigned_to);
            foreach ($employees as $employee) {
                $employeeIds[] = $employee;
            }
        }
        $data = [];
        $users =  User::whereIn('id', array_unique($employeeIds))->get();
        foreach($users as $user)
        {

            $data[$user->id]['name']        = $user->name;
            $data[$user->id]['avatar']      = $user->avatar;
        }
        return $data;
    }

    public static function getTeams($id)
    {
        $advName = User::whereIn('id', explode(',', $id))->pluck('name')->toArray();
        return implode(', ', $advName);
    }
}
