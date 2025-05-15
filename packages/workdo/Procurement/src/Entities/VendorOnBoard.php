<?php

namespace Workdo\Procurement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\Account\Entities\Vender;
class VendorOnBoard extends Model
{
    use HasFactory;

    protected $fillable = [
        'application',
        'joining_date',
        'status',
        'convert_to_vendor',
        'workspace',
        'created_by',
        'created_at',
        'updated_at',
    ];
    
    protected static function newFactory()
    {
        return \Workdo\Procurement\Database\factories\VendorOnBoardFactory::new();
    }

    public function applications()
    {
        return $this->hasOne(RfxApplication::class, 'id', 'application');
    }
    public static $status = [
        '' => 'Select Status',
        'pending' => 'Pending',
        'cancel' => 'Cancel',
        'confirm' => 'Confirm',
    ];

    public static $rfx_type = [
        '' => 'Select RFx Type',
        'RFI' => 'RFI',
        'RFQ' => 'RFQ',
        'RFP' => 'RFP',
    ];

    public static $budget_duration = [
        '' => 'Select Budget Duration',
        'monthly' => 'Monthly',
        'weekly' => 'Weekly',
    ];

    function vendorNumber()
    {
        $latest = Vender::where('workspace',getActiveWorkSpace())->latest()->first();
        if (!$latest)
        {
            return 1;
        }
        return $latest->vendor_id + 1;
    }
}
