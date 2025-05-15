<?php

namespace Workdo\BeverageManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\ProductService\Entities\ProductService;

class QualityStandard extends Model
{
    use HasFactory;

    protected $table = 'quality_standards';

    protected $guarded = [];

    public function productService()
    {
        if (module_is_active('ProductService')) {
            return $this->hasOne(ProductService::class, 'id', 'item_id');
        }
    }

}
