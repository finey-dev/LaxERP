<?php

namespace Workdo\Facilities\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\ProductService\Entities\ProductService;
use App\Models\User;

class FacilitiesBooking extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function service_name()
    {
        return $this->hasOne(ProductService::class, 'id', 'service');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'client_id');
    }
}
