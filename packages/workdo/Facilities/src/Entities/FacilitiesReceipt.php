<?php

namespace Workdo\Facilities\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\ProductService\Entities\ProductService;
use Workdo\Facilities\Entities\FacilitiesBooking;
use App\Models\User;

class FacilitiesReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'client_id',
        'name',
        'service',
        'number',
        'gender',
        'start_time',
        'end_time',
        'price',
        'payment_type',
        'workspace',
        'created_by',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'client_id');
    }

    public function services()
    {
        return $this->hasOne(ProductService::class, 'id', 'service');
    }

    public function booking()
    {
        return $this->hasOne(FacilitiesBooking::class, 'id', 'booking_id');
    }
}
