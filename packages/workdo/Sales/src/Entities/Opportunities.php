<?php

namespace Workdo\Sales\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Opportunities extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'account',
        'stage',
        'amount',
        'probability',
        'close_date',
        'contacts',
        'lead_source',
        'description',
        'workspace',
        'created_by',
    ];

    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function stages()
    {
        return $this->hasOne('Workdo\Sales\Entities\OpportunitiesStage', 'id', 'stage');
    }

    public function accounts()
    {
        return $this->hasOne('Workdo\Sales\Entities\SalesAccount', 'id', 'account');
    }

    public function leadsource()
    {
        return $this->hasOne('Workdo\Lead\Entities\Source', 'id', 'lead_source');
    }
}
