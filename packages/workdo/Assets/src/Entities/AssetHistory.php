<?php

namespace Workdo\Assets\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetHistory extends Model
{
    use HasFactory;

    protected $table = 'asset_historys';
    protected $fillable = [
        'id',
        'assets_id',
        'type',
        'quantity',
        'date',
        'created_by',
        'workspace_id',
    ];

    public function modules()
    {
       return $this->hasOne(Asset::class, 'id','assets_id');

    }

}
