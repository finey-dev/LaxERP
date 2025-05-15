<?php

namespace Workdo\Procurement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RfxApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfx',
        'name',
        'email',
        'phone',
        'profile',
        'proposal',
        'cover_letter',
        'dob',
        'gender',
        'country',
        'state',
        'city',
        'stage',
        'order',
        'skill',
        'rating',
        'is_archive',
        'custom_question',
        'bid_type',
        'bid_total',
        'billing_type',
        'bid_total_amount',
        'workspace',
        'created_by',
    ];
    
    protected static function newFactory()
    {
        return \Workdo\Procurement\Database\factories\RfxApplicationFactory::new();
    }

    public function rfxs()
    {
        return $this->hasOne(Rfx::class, 'id', 'rfx');
    }
    
    public function stages()
    {
        return $this->hasOne(RfxStage::class, 'id', 'stage');
    }

    public static $application_type = [
        '' => 'Select Application Type',
        'new' => 'New',
        'rfx_applicant' => 'RFX Applicant',
    ];
    public static $bid_type = [
        '' => 'Select Bid Type',
        'competitive' => 'Competitive',
        'non-competitive' => 'Non-Competitive',
    ];
}
