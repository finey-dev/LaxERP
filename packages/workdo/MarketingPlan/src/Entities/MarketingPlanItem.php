<?php

namespace Workdo\MarketingPlan\Entities;

use Illuminate\Database\Eloquent\Model;
use Workdo\ProductService\Entities\ProductService;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarketingPlanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'marketing_plan_id',
        'item_type',
        'item',
        'workspace',
        'created_by',
    ];

    public static $item_type = [
        'Products' =>'Products' ,
        'Services' => 'Services',
        'Parts' => 'Parts',
        'Rent' => 'Rent',
        'Music institute' => 'Music institute',
        'Restaurants' => 'Restaurants',
        'Bookings' => 'Bookings',
        'Fleet' => 'Fleet',
    ];

    public function Items()
    {
        return $this->hasOne(ProductService::class, 'id', 'item');
    }

}
