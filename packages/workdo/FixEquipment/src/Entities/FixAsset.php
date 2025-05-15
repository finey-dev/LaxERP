<?php

namespace Workdo\FixEquipment\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FixAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'asset_image',
        'purchase_date',
        'purchase_price',
        'location',
        'manufacturer',
        'category',
        'serial_number',
        'model_name',
        'supplier',
        'depreciation_method',
        'status',
        'license',
        'accessories',
        'maintenance',
        'description',
        'audit',
        'account'
    ];


    public function EquipmentStatus()
    {
        return $this->hasOne(EquipmentStatus::class, 'id', 'status');
    }

    public function equipmentCategory()
    {
        return $this->hasOne(EquipmentCategory::class, 'id', 'category');
    }

    public function Manufacturer()
    {
        return $this->hasOne(Manufacturer::class, 'id', 'manufacturer');
    }

    public function Supplier()
    {
        return $this->hasOne(User::class, 'id', 'supplier');
    }

    public function Location()
    {
        return $this->hasOne(EquipmentLocation::class, 'id', 'location');
    }

    public function Depreciation()
    {
        return $this->hasOne(Depreciation::class, 'id', 'depreciation_method');
    }
}
