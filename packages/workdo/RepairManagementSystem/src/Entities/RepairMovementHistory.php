<?php

namespace Workdo\RepairManagementSystem\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepairMovementHistory extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    protected static function newFactory()
    {
        return \Workdo\RepairManagementSystem\Database\factories\RepairMovementHistoryFactory::new();
    }

    public function repairOrderRequest()
    {
        return $this->hasOne(RepairOrderRequest::class, 'id', 'repair_id');
    }

    public static function movementHistoryStore($repair_id, $from, $to, $reason)
    {
        $movement_history                  = New RepairMovementHistory();
        $movement_history->repair_id       = $repair_id;
        $movement_history->date_time       = date('Y-m-d H:i:s');
        $movement_history->movement_from   = $from;
        $movement_history->movement_to     = $to;
        $movement_history->movement_reason = $reason;
        $movement_history->workspace       = getActiveWorkSpace();
        $movement_history->created_by      = creatorId();
        $movement_history->save();
    }
}
