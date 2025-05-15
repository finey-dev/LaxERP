<?php

namespace Workdo\Assets\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetExtra extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'asset_id',
        'code',
        'quantity',
        'date',
        'description',
    ];

}
