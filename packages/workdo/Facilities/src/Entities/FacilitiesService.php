<?php

namespace Workdo\Facilities\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\ProductService\Entities\ProductService;

class FacilitiesService extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function item()
    {
        return $this->hasOne(ProductService::class, 'id', 'item_id');
    }
}
