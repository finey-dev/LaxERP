<?php

namespace Workdo\BeverageManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Packaging extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Workdo\BeverageManagement\Database\factories\PackagingFactory::new();
    }

    public function manufacture()
    {
        return $this->hasOne(Manufacturing::class, 'id', 'manufacturing_id');
    }

    public function collectionCenter()
    {
        return $this->hasOne(CollectionCenter::class, 'id', 'collection_center_id');
    }
}
