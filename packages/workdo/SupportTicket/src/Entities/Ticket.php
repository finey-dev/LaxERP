<?php

namespace Workdo\SupportTicket\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'name',
        'email',
        'user_id',
        'account_type',
        'category',
        'subject',
        'status',
        'description',
        'attachments',
        'note',
        'created_by',
        'workspace_id'
    ];

    public function conversions()
    {
        return $this->hasMany('Workdo\SupportTicket\Entities\Conversion', 'ticket_id', 'id')->orderBy('id');
    }

    public function tcategory()
    {
        return $this->hasOne('Workdo\SupportTicket\Entities\TicketCategory', 'id', 'category');
    }

    public function workspace(){
        return $this->hasOne('App\Models\WorkSpace', 'id', 'workspace_id');
    }
    public static function category($category)
    {
        $categoryArr  = explode(',', $category);
        $unitRate = 0;
        foreach($categoryArr as $username)
        {
            $category     = TicketCategory::find($category);
            $unitRate     = $category->name;
        }

        return $unitRate;
    }

}
