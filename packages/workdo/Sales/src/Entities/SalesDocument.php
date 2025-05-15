<?php

namespace Workdo\Sales\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'Folder',
        'type',
        'status',
        'publish_date',
        'expiration_date',
        'description',
        'workspace',
        'attachments',
    ];
    public static $status = [
        'Active',
        'Draft',
        'Expired',
        'Canceled',
    ];

    public function assign_user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
    public function accounts()
    {
        return $this->hasOne('Workdo\Sales\Entities\SalesAccount', 'id', 'account');
    }
    public function types()
    {
        return $this->hasOne('Workdo\Sales\Entities\SalesDocumentType', 'id', 'type');
    }

    public function opportunitys(){
        return $this->belongsTo('Workdo\Sales\Entities\Opportunities','opportunities','id');
    }

}
