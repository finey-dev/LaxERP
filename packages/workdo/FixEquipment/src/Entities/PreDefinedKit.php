<?php

namespace Workdo\FixEquipment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PreDefinedKit extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'asset',
        'component',
        'created_by',
        'workspace'
    ];

    public function fixAsset(){
        return $this->hasOne(FixAsset::class, 'id', 'asset');;
    }

    public function assetComponents(){
        return $this->hasOne(AssetComponents::class, 'id', 'component');
    }
}
