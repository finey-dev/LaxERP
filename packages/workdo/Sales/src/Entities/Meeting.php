<?php

namespace Workdo\Sales\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'status',
        'start_date',
        'end_date',
        'parent',
        'parent_id',
        'account',
        'description',
        'attendees_user',
        'attendees_contact',
        'attendees_lead',
        'workspace',
    ];

    public static $status   = [
        'Planned',
        'Held',
        'Not Held',
    ];
    public static $parent   = [
        'account' => 'Account',
        // 'lead' => 'Lead',
        'contact' => 'Contact',
        'opportunities' => 'Opportunities',
        'case' => 'Case',
    ];

    public function accountName()
    {
        return $this->hasOne('Workdo\Sales\Entities\SalesAccount', 'id', 'account');
    }

    public function leadName()
    {
        return $this->hasOne('Workdo\Lead\Entities\Lead', 'id', 'attendees_lead');
    }

    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function attendees_contacts()
    {
        return $this->hasOne('Workdo\Sales\Entities\Contact', 'id', 'attendees_contact');
    }
    public function attendees_users()
    {
        return $this->hasOne('App\Models\User', 'id', 'attendees_user');
    }

    public function getparent($type, $id)
    {
        if($type == 'account')
        {
            $parent = SalesAccount::find($id)->name;

        }
        elseif($type == 'contact')
        {
            $parent = Contact::find($id)->name;
        }
        elseif($type == 'opportunities')
        {
            $parent = Opportunities::find($id);

            if ($parent) {
                $parent = $parent->name;
            } else {
                return response()->json(['error' => 'Opportunity not found'], 404);
            }
        }
        elseif($type == 'case')
        {
            $parent = CommonCase::find($id)->name;
        }else{
            $parent= '';
        }

        return $parent;
    }

    public function attendees_leads()
    {
        if(module_is_active('Lead'))
        {
            return $this->hasOne(\Workdo\Lead\Entities\Lead::class, 'id', 'attendees_lead')->first();
        }
        else
        {
            return [];
        }
    }
}
