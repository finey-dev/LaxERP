<?php

namespace Workdo\SupportTicket\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id','description', 'attachments', 'sender'
    ];

    public function replyBy(){
        if($this->sender=='user'){
            return $this->ticket;
        }
        else{
            return $this->hasOne('App\Models\User','id','sender')->first();
        }
    }

    public function ticket(){
        return $this->hasOne('Workdo\SupportTicket\Entities\Ticket','id','ticket_id');
    }


}
