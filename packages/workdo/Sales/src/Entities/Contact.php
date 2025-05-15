<?php

namespace Workdo\Sales\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'account',
        'email',
        'phone',
        'contact_address',
        'contact_city',
        'contact_state',
        'contact_country',
        'contact_postalcode',
        'description',
        'workspace',
        'created_by',
    ];

    protected $appends = ['account_name'];
    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function assign_account()
    {
        return $this->hasOne('Workdo\Sales\Entities\SalesAccount', 'id', 'account');
    }

    public function getAccountNameAttribute()
    {
        $account = Contact::find($this->account);

        return $this->attributes['account_name'] = !empty($account) ? $account->name : '';
    }
}
