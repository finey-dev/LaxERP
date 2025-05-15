<?php

namespace Workdo\FixEquipment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EquipmentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category_type',
        'created_by',
        'workspace'
    ];

    public $categoryTypes = [
        'Asset'       => 'Asset',
        'Accessories' => 'Accessories',
        'Licence'     => 'Licence',
        'Components'  => 'Components',
        'Consumables' => 'Consumables'
    ];

    
}
