<?php

namespace Workdo\CourierManagement\Entities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\CourierManagement\Entities\PackageCategory;



class CourierPackageInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_id',
        'package_title',
        'package_description',
        'height',
        'width',
        'weight',
        'package_category',
        'tracking_status',
        'tracking_status_log',
        'price',
        'expected_delivery_date',
        'workspace_id',
        'created_by',
    ];
    
    public function courier_category()
    {
        return  $this->hasOne(PackageCategory::class, 'id', 'package_category');

    }


    public function getTrackingStatus(){
        return $this->hasOne(Trackingstatus::class,'id','tracking_status');
    }

    protected static function newFactory()
    {
        return \Workdo\CourierManagement\Database\factories\CourierPackageInfoFactory::new();
    }
    // public function category($id)
    // {
    //     $category = PackageCategory::find($id)->category;
    //     return $category;
    // }
}
